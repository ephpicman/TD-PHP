"use strict";
var Nex = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // src/App.ts
  var App_exports = {};
  __export(App_exports, {
    Str: () => Str_exports
  });

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
  return __toCommonJS(App_exports);
})();
//# sourceMappingURL=App.js.map
