import numpy as np
import pandas as pd
from flask import Flask, request, jsonify
from sklearn.preprocessing import MinMaxScaler
from keras.models import Sequential, load_model
from keras.layers import LSTM, Dense
import os

# Initialize the Flask app
app = Flask(__name__)

MODEL_PATH = 'lstm_model.h5'  # Path to save the model

# Function to create and train the LSTM model
def create_lstm_model(data):
    # Feature Scaling
    scaler = MinMaxScaler(feature_range=(0, 1))
    data_scaled = scaler.fit_transform(data.fillna(method='ffill'))  # Fill missing values with forward fill to avoid NaN
    
    # Reshape input data for LSTM (samples, time steps, features)
    X = data_scaled.reshape(data_scaled.shape[0], 1, data_scaled.shape[1])  # Reshape to 3D for LSTM
    y = data_scaled  # Output is the same data, as we're predicting the next time step

    # Define the LSTM model with 10 neurons in the hidden layer
    model = Sequential()
    model.add(LSTM(units=10, return_sequences=False, input_shape=(X.shape[1], X.shape[2])))
    model.add(Dense(units=5))  # Output layer with 5 neurons (one per feature)
    model.compile(optimizer='adam', loss='mean_squared_error')
    
    # Train the model (using data itself as target for illustration)
    model.fit(X, y, epochs=50, batch_size=1, verbose=0)  # Reduced epochs for optimization
    
    # Save the model
    model.save(MODEL_PATH)
    
    return model, scaler

# Function to predict the next time step (future measurement)
def predict_next_measurement(data, model, scaler):
    # Feature Scaling
    data_scaled = scaler.transform(data.fillna(method='ffill'))  # Ensure missing values are filled and scaled
    
    # Reshape input data for LSTM (samples, time steps, features)
    X = data_scaled.reshape(data_scaled.shape[0], 1, data_scaled.shape[1])
    
    # Predict the next time step (future measurement)
    predicted_value = model.predict(X)
    
    # Inverse transform to get the original scale
    predicted_value_original_scale = scaler.inverse_transform(predicted_value)
    
    # Return the predicted values
    return predicted_value_original_scale

# Function to pad the data arrays to make them the same length
def pad_data(input_data):
    # Find the length of the longest array
    max_length = max(len(v) for v in input_data.values())
    
    # Pad the shorter arrays with NaN to make all arrays the same length
    padded_data = {}
    for key, values in input_data.items():
        if len(values) < max_length:
            # Pad the array with NaN to match the max length
            padded_data[key] = values + [np.nan] * (max_length - len(values))
        else:
            padded_data[key] = values
    
    return padded_data

# Function to handle empty arrays
def handle_empty_arrays(input_data):
    for key, values in input_data.items():
        if not values:  # If the array is empty
            input_data[key] = [0]  # Set the value to 0
    return input_data

# Load the model once when the application starts
if os.path.exists(MODEL_PATH):
    model = load_model(MODEL_PATH)
    print(f"Model loaded from {MODEL_PATH}")
else:
    model = None
    print("Model not found. Will train a new one.")

# API endpoint to predict the next measurement based on historical data
@app.route('/ai/predict', methods=['POST'])
def predict():
    global model
    
    # Get data from the request
    input_data = request.json
    
    # Handle empty arrays
    input_data = handle_empty_arrays(input_data)
    
    # Pad the data to ensure all arrays are of the same length
    padded_data = pad_data(input_data)
    
    # Convert padded data to pandas DataFrame
    data = pd.DataFrame(padded_data)
    print(f"Original Data (Padded): {data}")  # Debugging line to check the input
    
    # If model is not loaded, train a new model and save it
    if model is None:
        model, scaler = create_lstm_model(data)
    else:
        # Use the loaded model to make predictions
        scaler = MinMaxScaler(feature_range=(0, 1))
        scaler.fit(data.fillna(method='ffill'))  # Refit scaler to the new data
    
    # Predict the next time step (future measurement)
    predicted_values = predict_next_measurement(data, model, scaler)
    
    # Debugging: print predicted values
    print(f"Predicted Values: {predicted_values}")
    
    # Prepare the response data (predicting the next time step)
    result = pd.DataFrame(predicted_values, columns=data.columns)
    
    # Return the predicted values as a single object
    return jsonify(result.iloc[0].to_dict())  # Return only the predicted values for the next time step

# Run the Flask app
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)
