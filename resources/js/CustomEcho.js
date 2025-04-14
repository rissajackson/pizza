import Echo from 'laravel-echo';
import ReverbConnector from './ReverbConnector.js';
import Pusher from "pusher-js";

export default class CustomEcho extends Echo {
    constructor(options) {
        super(options);

        // Override the broadcaster's connector with ReverbConnector explicitly
        if (options.broadcaster === 'reverb') {
            this.connector = new ReverbConnector(options);
        } else {
            throw new Error(
                `Unsupported broadcaster: ${options.broadcaster}. Use 'reverb' as the broadcaster.`
            );
        }
        window.Pusher = Pusher;
    }
}
