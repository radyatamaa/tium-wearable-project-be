@auth()
@include('layouts.navbars.navs.auth')
@endauth

@guest()
@include('layouts.navbars.navs.guest')
@endguest

<script>
setInterval(checkSession, 5000);
checkSession();

function checkSession() {
    fetch('/public-health-center/check-session')
        .then(response => {
            const redirectUrl = response.url;
            if (redirectUrl.includes('/login')) {
                // Jika respons statusnya 302, tampilkan modal
                if (!window.location.href.includes('/login')) {
                    $('#errorSessionLogoutPopupModal').modal('show');
                }
            } else if (response.ok) {
                return response.json();
            } else {
                throw new Error('Something went wrong');
            }
        })
        .then(data => {});
}
</script>