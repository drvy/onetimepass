class OTCMessage {
    message         = '';
    passphrase      = '';

    /**
     * Json Formatter for the CryptJS AES functionality.
     * https://cryptojs.gitbook.io/docs/
     */
    jsonFormatter = {
        stringify: (cipherParams) => {
            let jsonObj = {
                ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)
            };

            if (cipherParams.iv) {
                jsonObj.iv = cipherParams.iv.toString();
            }

            if (cipherParams.salt) {
                jsonObj.s = cipherParams.salt.toString();
            }

            return JSON.stringify(jsonObj);
        },


        parse: function(jsonStr) {
            let jsonObj      = JSON.parse(jsonStr);
            let cipherParams = CryptoJS.lib.CipherParams.create({
                ciphertext: CryptoJS.enc.Base64.parse(jsonObj.ct)
            });

            if (jsonObj.iv) {
                cipherParams.iv = CryptoJS.enc.Hex.parse(jsonObj.iv);
            }

            if (jsonObj.s) {
                cipherParams.salt = CryptoJS.enc.Hex.parse(jsonObj.s);
            }

            return cipherParams;
          }
    };

    constructor(message, passphrase) {
        if (typeof CryptoJS === 'undefined') {
            console.log('CryptoJS is not loaded. Errors will ensure');
            return false;
        }

        this.message    = message;
        this.passphrase = passphrase;
    }


    /**
     * Encrypt a message with a specific passphrase.
     *
     * @returns {string}
     */
    encrypt() {
        let _this  = this;
        let secret = CryptoJS.AES.encrypt(this.message, this.passphrase, {
            format: _this.jsonFormatter
        });

        return secret.toString();
    };


    /**
     * Decrypt a message with a specific passphrase
     *
     * @returns {string}
     */
    decrypt() {
        let _this  = this;
        let secret = CryptoJS.AES.decrypt(this.message, this.passphrase, {
            format: _this.jsonFormatter
        });

        return secret.toString(CryptoJS.enc.Utf8);
    }
};
