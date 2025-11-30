INSTALL ON LINUX :

-   sudo add-apt-repository ppa:deadsnakes/ppa -y
-   sudo apt update
-   sudo apt install python3.10 python3.10-venv python3.10-dev -y
-   cd /home/tiumwatch/tium-wearable-project-be/ai
-   python3.10 -m venv venv
-   source venv/bin/activate
-   pip install numpy pandas tensorflow flask scikit-learn
-   python main.py
-   test api with curl : curl -X POST http://localhost:5001/predict -H "Content-Type: application/json" -d '{
    "systolic": [120, 125, 130, 135, 140, 145, 150, 155, 160, 165],
    "diastolic": [80, 85, 90, 95, 100, 105, 110, 115, 120, 125],
    "temperature": [36.5, 36.6, 36.7, 36.8, 36.9, 37.0, 37.1, 37.2, 37.3, 37.4],
    "saturation": [98, 97, 96, 95, 94, 93, 92, 91, 90, 89],
    "bpm": [75, 80, 85, 90, 95, 100, 105, 110, 115, 120]
    }
    '
