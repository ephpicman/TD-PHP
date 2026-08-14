/**
 * Returns the number of UTF-16 code units in a string.
 *
 * Equivalent to JavaScript's native `String.length`.
 *
 * Examples:
 * "Hello"       -> 5
 * "😀"          -> 2
 * "🇮🇷"          -> 4
 * "👨‍👩‍👧‍👦"      -> 11
 */
export function length(
    value: string
): number {

    return value.length;

}


/**
 * Returns the number of Unicode code points in a string.
 *
 * Examples:
 * "Hello"       -> 5
 * "😀"          -> 1
 * "🇮🇷"          -> 2
 * "👨‍👩‍👧‍👦"      -> 7
 */
export function codePointLength(
    value: string
): number {

    return [...value].length;

}


/**
 * Returns the number of user-perceived characters (grapheme clusters).
 *
 * Uses Intl.Segmenter when available.
 *
 * Examples:
 * "Hello"       -> 5
 * "😀"          -> 1
 * "🇮🇷"          -> 1
 * "👨‍👩‍👧‍👦"      -> 1
 * "é"          -> 1
 */
export function graphemeLength(
    value: string
): number {

    if (
        typeof Intl !== "undefined" &&
        typeof Intl.Segmenter === "function"
    ) {

        const segmenter = new Intl.Segmenter(
            undefined,
            {
                granularity: "grapheme"
            }
        );

        return [...segmenter.segment(value)].length;

    }

    // Fallback for environments without Intl.Segmenter.
    return codePointLength(value);

}