import {Logger} from "../src/dependencies/Logger";

export class CapturingLogger implements Logger {
    lines: Array<string> = [];

    info(message: string): void {
        this.lines.push("INFO: " + message);
    }

    error(message: string): void {
        this.lines.push("ERROR: " + message);
    }
}
