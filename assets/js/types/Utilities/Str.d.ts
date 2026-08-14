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
export declare function length(value: string): number;
/**
 * Returns the number of Unicode code points in a string.
 *
 * Examples:
 * "Hello"       -> 5
 * "😀"          -> 1
 * "🇮🇷"          -> 2
 * "👨‍👩‍👧‍👦"      -> 7
 */
export declare function codePointLength(value: string): number;
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
export declare function graphemeLength(value: string): number;
