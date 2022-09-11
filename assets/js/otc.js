class OTC {
    _keySize       = 64; // * 8 = 512
    _keyIterations = 1000;
    _salt          = '';

    /**
     * Json Formatter for the CryptJS AES functionality.
     * https://cryptojs.gitbook.io/docs/
     */
    jsonFormatter = {
        stringify: (cipherParams) => {
            let jsonObj = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};

            if (cipherParams.iv) {
                jsonObj.iv = cipherParams.iv.toString();
            }

            if (cipherParams.salt) {
                jsonObj.s = cipherParams.salt.toString();
            }

            return JSON.stringify(jsonObj);
        },


        parse: (jsonStr) => {
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


    constructor() {
        if (typeof CryptoJS === 'undefined') {
            alert('Failed to load CryptoJS.');
            return;
        }

        this._salt = CryptoJS.lib.WordArray.random(128/8);

        if (OTCSettings) {
            this._keySize       = (OTCSettings.keySize ? parseInt(OTCSettings.keySize) : this._keySize);
            this._keyIterations = (OTCSettings.rotations ? parseInt(OTCSettings.rotations) : this._keyIterations);
            this._salt          = (OTCSettings.salt ? OTCSettings.salt : this._salt);
        }
    };


    /**
     * Make a request but
     * @param {*} endpoint
     * @param {*} callback
     * @param {*} data
     * @param {*} method
     */
    _request(endpoint, callback, data, method) {
        fetch(endpoint, {
            method: (method ? method : 'GET'),
            headers: {
                'Content-Type': 'application/json'
            },
            body: (data ? data : undefined)
        }).then((response) => response.json()).then((data) => {
            if (!data.success) {
                throw new Error((data.errorMsg ? data.errorMsg : 'Undefined error'));
            }

            callback(data);
        }).catch((error) => {
            console.log(error);
            callback({'status': false, error: error.message});
        });
    };


    /**
     *
     * @param {*} endpoint
     * @param {*} callback
     * @param {*} data
     * @param {*} method
     */
    request(endpoint, callback, data, method) {
        let _this = this;

        if (data && method !== 'GET') {
            _this._request('/rest/csrf', (csrf) => {
                data = Object.assign(csrf, data);
                _this._request(endpoint, callback, data, method);
                return;
            });
        }

        _this._request(endpoint, callback);
    };


    /**
     *
     * @param {*} passphrase
     * @returns
     */
    makeKey(passphrase) {
        return CryptoJS.PBKDF2(passphrase, this._salt, {
            keySize: this._keySize,
            iterations: this._keyIterations
        }).toString();
    };


    /**
     *
     * @param {*} message
     * @param {*} passphrase
     * @returns
     */
    encryptMessage(message, passphrase) {
        return CryptoJS.AES.encrypt(
            message,
            this.makeKey(passphrase),
            {
                format: this.jsonFormatter
            }
        ).toString();
    };


    /**
     *
     * @param {*} message
     * @param {*} passphrase
     * @returns
     */
    decryptMessage(message, passphrase) {
        message = this.jsonFormatter.parse(message);
        return CryptoJS.AES.decrypt(
            message,
            this.makeKey(passphrase)
        ).toString(CryptoJS.enc.Utf8);
    };
};
