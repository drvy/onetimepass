let addFilesBtn = document.getElementById('addFiles');
let filesField  = document.getElementById('files');
let generateBtn = document.getElementById('generate');

generateBtn.addEventListener('click', function (event) {
    event.preventDefault();

    let message    = document.getElementById('message').value;
    let passphrase = document.getElementById('passphrase').value;

    try {
        let secret  = new OTCMessage(message, passphrase).encrypt();
        let token   = new OTCToken(passphrase).get();

        let request = new OTCRequest().post('/rest/message/', {secret: secret, token: token}, function (status, response) {
            console.log(status, response);
        });

    } catch (error) {
        console.log(error);
    }
});


addFilesBtn.addEventListener('click', function (event) {
    filesField.classList.add('display');
    addFilesBtn.classList.remove('display');
});
