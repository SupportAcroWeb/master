BX.ready(function() {
    let form = document.querySelector('#sender-subscribe-form form');
    let messageElement = document.querySelector('#sender-subscribe-form .sender-subscribe-message');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let email = this.querySelector('input[name="email"]').value;

            BX.ajax.runComponentAction('acroweb:sender.subscribe', 'subscribe', {
                mode: 'class',
                data: {email: email}
            }).then(function(response) {
                if (response.data) {
                    messageElement.textContent = response.data.message;
                    messageElement.style.display = 'block';
                    messageElement.style.color = 'green';
                    form.style.display = 'none';
                }
            }, function(response) {
                let errorMessage = response.errors[0].message;
                messageElement.textContent = errorMessage;
                messageElement.style.display = 'block';
                messageElement.style.color = 'red';
            });
        });
    }
});
