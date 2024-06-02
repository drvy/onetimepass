class OTCToken {
    keySize    = 8;
    iterations = 1000;
    message    = '';
    salt       = '';

    constructor(message) {
        if (clientSettings) {
            this.keySize    = parseInt(clientSettings.keySize) || this.iterations;
            this.iterations = parseInt(clientSettings.rotations) || this.keySize;
            this.salt       = clientSettings.salt || CryptoJS.lib.WordArray.random(128/8);
        }

        this.message = message;
    }

    get() {
        let token = CryptoJS.PBKDF2(this.message, this.salt, {
            keySize: this.keySize,
            iterations: this.iterations
        });

        return token.toString();
    }
}
