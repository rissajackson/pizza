import { Connector } from 'laravel-echo';

/**
 * Custom WebSocket connector for Reverb.
 * Extends Laravel Echo's Connector to handle WebSocket connections.
 * @extends {Connector}
 */
export default class ReverbConnector extends Connector {
    /**
     * @param {object} options - Configuration options for the WebSocket connection.
     */
    constructor(options) {
        super(options);

        /**
         * The WebSocket connection instance.
         * @type {WebSocket | null}
         */
        this.socket = null;

        console.log('ReverbConnector Initialized with options:', options);
    }

    /**
     * Establishes a connection to the WebSocket server.
     * @returns {WebSocket} - The WebSocket instance.
     */
    connect() {
        this.socket = new WebSocket(
            `${this.options.forceTLS ? 'wss' : 'ws'}://${this.options.wsHost}:${this.options.wsPort}`
        );

        this.socket.onopen = () => {
            console.log('Connected to Reverb WebSocket server.');
        };

        this.socket.onclose = () => {
            console.log('Disconnected from Reverb WebSocket server.');
        };

        this.socket.onerror = (error) => {
            console.error('Reverb WebSocket connection error:', error);
        };

        return this.socket;
    }

    /**
     * Disconnects from the WebSocket server.
     */
    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
            console.log('Manually disconnected from Reverb WebSocket server.');
        }
    }

    /**
     * Subscribes to a WebSocket channel.
     * This mimics the behavior of Laravel Echo's subscription system.
     * @param {string} channel - The channel to subscribe to.
     * @returns {object} - An object with a `listen` method for subscribing to events.
     */
    subscribe(channel) {
        console.log(`Subscribed to channel: ${channel}`);
        return {
            listen: (event, callback) => this.listen(channel, event, callback),
        };
    }

    /**
     * Unsubscribes from a WebSocket channel.
     * @param {string} channel - The channel to unsubscribe from.
     */
    unsubscribe(channel) {
        console.log(`Unsubscribed from channel: ${channel}`);
        // Place any cleanup logic for unsubscribing if necessary.
    }

    /**
     * Listens for a specific event on a channel.
     * @param {string} channel - The channel name.
     * @param {string} event - The event name to listen for.
     * @param {Function} callback - The callback function for handling the event data.
     */
    listen(channel, event, callback) {
        // Ensure connection is established.
        if (!this.socket) {
            console.warn('WebSocket is not connected. Establishing a new connection...');
            this.connect();
        }

        // Handle WebSocket messages for the specified channel and event.
        this.socket.onmessage = (message) => {
            try {
                const data = JSON.parse(message.data);

                // Match the channel and the event before invoking the callback.
                if (data.channel === channel && data.event === event) {
                    callback(data.payload);
                }
            } catch (error) {
                console.error('Failed to parse WebSocket message:', error);
            }
        };
    }
}
