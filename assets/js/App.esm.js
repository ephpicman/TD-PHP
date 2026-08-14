var __defProp = Object.defineProperty;
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};

// src/Utilities/Str.ts
var Str_exports = {};
__export(Str_exports, {
  codePointLength: () => codePointLength,
  graphemeLength: () => graphemeLength,
  length: () => length
});
function length(value) {
  return value.length;
}
function codePointLength(value) {
  return [...value].length;
}
function graphemeLength(value) {
  if (typeof Intl !== "undefined" && typeof Intl.Segmenter === "function") {
    const segmenter = new Intl.Segmenter(
      void 0,
      {
        granularity: "grapheme"
      }
    );
    return [...segmenter.segment(value)].length;
  }
  return codePointLength(value);
}
export {
  Str_exports as Str
};
//# sourceMappingURL=App.esm.js.map
