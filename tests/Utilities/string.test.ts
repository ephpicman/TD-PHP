import {
    length,
    codePointLength,
    graphemeLength
} from "./../../src/Utilities/Str";

import {
    describe,
    expect,
    it
} from "vitest";


describe("String.length()", () => {

    it("counts UTF-16 code units", () => {

        expect(length("Hello")).toBe(5);
        expect(length("سلام")).toBe(4);
        expect(length("😀")).toBe(2);
        expect(length("🇮🇷")).toBe(4);
        expect(length("👨‍👩‍👧‍👦")).toBe(11);

    });

});


describe("String.codePointLength()", () => {

    it("counts Unicode code points", () => {

        expect(codePointLength("Hello")).toBe(5);
        expect(codePointLength("سلام")).toBe(4);
        expect(codePointLength("😀")).toBe(1);
        expect(codePointLength("🇮🇷")).toBe(2);
        expect(codePointLength("👨‍👩‍👧‍👦")).toBe(7);

    });

});


describe("String.graphemeLength()", () => {

    it("counts grapheme clusters", () => {

        expect(graphemeLength("Hello")).toBe(5);
        expect(graphemeLength("سلام")).toBe(4);
        expect(graphemeLength("😀")).toBe(1);
        expect(graphemeLength("🇮🇷")).toBe(1);
        expect(graphemeLength("👨‍👩‍👧‍👦")).toBe(1);
        expect(graphemeLength("é")).toBe(1);

    });

});