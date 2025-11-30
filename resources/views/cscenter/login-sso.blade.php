<!doctype html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <title>네이버 로그인</title>
    <script type="text/javascript" src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.3.js" charset="utf-8">
    </script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
</head>

<body>
    <button id="kakao-login-btn">Login with Kakao</button>
    <!-- 네이버 로그인 버튼 노출 영역 -->
    <div id="naver_id_login"></div>
    <!-- //네이버 로그인 버튼 노출 영역 -->
    <script type="text/javascript">
        var naver_id_login = new naver_id_login("k425JtDeAchw6TM7_Qow", "https://admin.tiumnco.kr/oauth-callback/naver");
        var state = naver_id_login.getUniqState();
        naver_id_login.setButton("white", 2, 40);
        naver_id_login.setDomain("admin.tiumnco.kr");
        naver_id_login.setState(state);
        naver_id_login.setPopup();
        naver_id_login.init_naver_id_login();
    </script>

    <script>
        const kakaoClientId = '6b34cc398a8a9ddb93e20c7e98da0a8a';
        const redirectUri =
            'https://admin.tiumnco.kr/oauth-callback/kakao'; // Must match the one registered in Naver/Kakao Console

        // Kakao Login Event
        document.getElementById('kakao-login-btn').addEventListener('click', () => {
            const kakaoAuthUrl =
                `https://kauth.kakao.com/oauth/authorize?response_type=code&client_id=${kakaoClientId}&redirect_uri=${redirectUri}`;
            window.location.href = kakaoAuthUrl;
        });

        // Function to extract the authorization code from the URL
        function getAuthorizationCode() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            if (code) {
                document.getElementById('token-output').textContent = `Authorization Code: ${code}`;
            }
        }

        // Call the function on page load to capture the code from redirected URL
        window.onload = getAuthorizationCode;
    </script>



    <button id="google-login-btn">Login with Google</button>
    <pre id="token-output"></pre>
    <script type="module">
        // Import necessary Firebase functions
        import {
            initializeApp
        } from 'https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js';
        import {
            getAuth,
            GoogleAuthProvider,
            signInWithPopup
        } from 'https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js';

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyB27xNWdWqhqGfg8qsn8ir3_okpYgTecJ8",
            authDomain: "tium-watch.firebaseapp.com",
            projectId: "tium-watch",
            storageBucket: "tium-watch.firebasestorage.app",
            messagingSenderId: "862770635628",
            appId: "1:862770635628:web:e4e5907bb8ed810a438949",
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Google Login Handler
        const googleLoginBtn = document.getElementById('google-login-btn');
        const tokenOutput = document.getElementById('token-output');

        googleLoginBtn.addEventListener('click', () => {
            const provider = new GoogleAuthProvider();
            signInWithPopup(auth, provider)
                .then((result) => {
                    // Get ID Token
                    return result.user.getIdToken();
                })
                .then((idToken) => {
                    // Output the ID Token
                    tokenOutput.textContent = `ID Token: ${idToken}`;
                })
                .catch((error) => {
                    console.error('Error during login:', error);
                });
        });
    </script>

</html>