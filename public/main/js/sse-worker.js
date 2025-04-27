// sse-worker.js

let eventSource = null;
let retryTimeout = 1000;

function connect() {
    eventSource = new EventSource(self.originRoute);

    eventSource.onmessage = (event) => {
        self.postMessage({ type: 'message', data: event.data });
    };

    eventSource.onerror = (errorEvent) => {
        self.postMessage({
            type: 'error',
            message: errorEvent.message || "SSE Error"
        });

        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }

        setTimeout(() => {
            connect();
            retryTimeout = Math.min(retryTimeout * 2, 10000);
        }, retryTimeout);
    };


    eventSource.onopen = () => {
        self.postMessage({ type: 'open' });
        retryTimeout = 5000;
    };
}

self.onmessage = (e) => {
    const { type, originRoute } = e.data;

    if (type === 'start') {
        self.originRoute = originRoute;
        connect();
    } else if (type === 'stop') {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }
        self.close();
    }
};
