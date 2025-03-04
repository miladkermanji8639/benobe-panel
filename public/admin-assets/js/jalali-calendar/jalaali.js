!(function (e) {
 if ('object' == typeof exports && 'undefined' != typeof module)
  module.exports = e();
 else if ('function' == typeof define && define.amd) define([], e);
 else {
  ('undefined' != typeof window
   ? window
   : 'undefined' != typeof global
     ? global
     : 'undefined' != typeof self
       ? self
       : this
  ).jalaali = e();
 }
})(function () {
 return (function e(n, r, t) {
  function a(i, u) {
   if (!r[i]) {
    if (!n[i]) {
     var f = 'function' == typeof require && require;
     if (!u && f) return f(i, !0);
     if (o) return o(i, !0);
     var l = new Error("Cannot find module '" + i + "'");
     throw ((l.code = 'MODULE_NOT_FOUND'), l);
    }
    var c = (r[i] = { exports: {} });
    n[i][0].call(
     c.exports,
     function (e) {
      return a(n[i][1][e] || e);
     },
     c,
     c.exports,
     e,
     n,
     r,
     t,
    );
   }
   return r[i].exports;
  }
  for (
   var o = 'function' == typeof require && require, i = 0;
   i < t.length;
   i++
  )
   a(t[i]);
  return a;
 })(
  {
   1: [
    function (e, n, r) {
     n.exports = {
      toJalaali: function (e, n, r) {
       '[object Date]' === Object.prototype.toString.call(e) &&
        ((r = e.getDate()), (n = e.getMonth() + 1), (e = e.getFullYear()));
       return l(c(e, n, r));
      },
      toGregorian: a,
      isValidJalaaliDate: function (e, n, r) {
       return (
        e >= -61 && e <= 3177 && n >= 1 && n <= 12 && r >= 1 && r <= i(e, n)
       );
      },
      isLeapJalaaliYear: o,
      jalaaliMonthLength: i,
      jalCal: u,
      j2d: f,
      d2j: l,
      g2d: c,
      d2g: d,
      jalaaliToDateObject: g,
      jalaaliWeek: function (e, n, r) {
       var t = g(e, n, r).getDay(),
        a = 6 == t ? 0 : -(t + 1),
        o = 6 + a;
       return { saturday: l(f(e, n, r + a)), friday: l(f(e, n, r + o)) };
      },
     };
     var t = [
      -61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097,
      2192, 2262, 2324, 2394, 2456, 3178,
     ];
     function a(e, n, r) {
      return d(f(e, n, r));
     }
     function o(e) {
      return (
       0 ===
       (function (e) {
        var n,
         r,
         a,
         o,
         i,
         u = t.length,
         f = t[0];
        if (e < f || e >= t[u - 1])
         throw new Error('Invalid Jalaali year ' + e);
        for (i = 1; i < u && ((r = (n = t[i]) - f), !(e < n)); i += 1) f = n;
        r - (o = e - f) < 6 && (o = o - r + 33 * y(r + 4, 33));
        -1 === (a = p(p(o + 1, 33) - 1, 4)) && (a = 4);
        return a;
       })(e)
      );
     }
     function i(e, n) {
      return n <= 6 ? 31 : n <= 11 || o(e) ? 30 : 29;
     }
     function u(e, n) {
      var r,
       a,
       o,
       i,
       u,
       f,
       l = t.length,
       c = e + 621,
       d = -14,
       g = t[0];
      if (e < g || e >= t[l - 1]) throw new Error('Invalid Jalaali year ' + e);
      for (f = 1; f < l && ((a = (r = t[f]) - g), !(e < r)); f += 1)
       (d = d + 8 * y(a, 33) + y(p(a, 33), 4)), (g = r);
      return (
       (d = d + 8 * y((u = e - g), 33) + y(p(u, 33) + 3, 4)),
       4 === p(a, 33) && a - u == 4 && (d += 1),
       (i = 20 + d - (y(c, 4) - y(3 * (y(c, 100) + 1), 4) - 150)),
       n
        ? { gy: c, march: i }
        : (a - u < 6 && (u = u - a + 33 * y(a + 4, 33)),
          -1 === (o = p(p(u + 1, 33) - 1, 4)) && (o = 4),
          { leap: o, gy: c, march: i })
      );
     }
     function f(e, n, r) {
      var t = u(e, !0);
      return c(t.gy, 3, t.march) + 31 * (n - 1) - y(n, 7) * (n - 7) + r - 1;
     }
     function l(e) {
      var n,
       r = d(e).gy,
       t = r - 621,
       a = u(t, !1);
      if ((n = e - c(r, 3, a.march)) >= 0) {
       if (n <= 185) return { jy: t, jm: 1 + y(n, 31), jd: p(n, 31) + 1 };
       n -= 186;
      } else (t -= 1), (n += 179), 1 === a.leap && (n += 1);
      return { jy: t, jm: 7 + y(n, 30), jd: p(n, 30) + 1 };
     }
     function c(e, n, r) {
      var t =
       y(1461 * (e + y(n - 8, 6) + 100100), 4) +
       y(153 * p(n + 9, 12) + 2, 5) +
       r -
       34840408;
      return (t = t - y(3 * y(e + 100100 + y(n - 8, 6), 100), 4) + 752);
     }
     function d(e) {
      var n, r, t, a;
      return (
       (n =
        (n = 4 * e + 139361631) +
        4 * y(3 * y(4 * e + 183187720, 146097), 4) -
        3908),
       (r = 5 * y(p(n, 1461), 4) + 308),
       (t = y(p(r, 153), 5) + 1),
       (a = p(y(r, 153), 12) + 1),
       { gy: y(n, 1461) - 100100 + y(8 - a, 6), gm: a, gd: t }
      );
     }
     function g(e, n, r, t, o, i, u) {
      var f = a(e, n, r);
      return new Date(f.gy, f.gm - 1, f.gd, t || 0, o || 0, i || 0, u || 0);
     }
     function y(e, n) {
      return ~~(e / n);
     }
     function p(e, n) {
      return e - ~~(e / n) * n;
     }
    },
    {},
   ],
   2: [
    function (e, n, r) {
     n.exports = e('./index.js');
    },
    { './index.js': 1 },
   ],
  },
  {},
  [2],
 )(2);
});
