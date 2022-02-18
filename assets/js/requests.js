class OTCRequest {
    request(endpoint, data, method, callback, csrf) {
        let request = new XMLHttpRequest();
            method  = (!method ? 'GET' : method);

        request.open(method, endpoint, true);
        request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        if (csrf) {
            data = Object.assign(data, csrf);
        }

        request.onload = () => {
            let responseText = JSON.parse(request.responseText);
            callback(request.status, responseText);
        };

        request.onerror = () => {
            callback(0);
        };

        request.send((data ? JSON.stringify(data) : undefined));
    };


    preRequest(endpoint, data, method, callback) {
        let _this = this;

        this.get('/rest/csrf', (status, csrf) => {
            if (status !== 200 || !csrf.success) {
                return false; // Todo error message.
            }

            _this.request(endpoint, data, 'POST', callback, csrf.response);
        });
    }


    get(endpoint, callback) {
        this.request(endpoint, undefined, 'GET', callback);
    };


    post(endpoint, data, callback) {
        this.preRequest(endpoint, data, 'POST', callback);
    };
};
