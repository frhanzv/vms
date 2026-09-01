var mf = { exports: {} }, Du = {};
var Am;
function l0() {
  if (Am) return Du;
  Am = 1;
  var M = /* @__PURE__ */ Symbol.for("react.transitional.element"), El = /* @__PURE__ */ Symbol.for("react.fragment");
  function P(g, cl, G) {
    var Al = null;
    if (G !== void 0 && (Al = "" + G), cl.key !== void 0 && (Al = "" + cl.key), "key" in cl) {
      G = {};
      for (var nl in cl)
        nl !== "key" && (G[nl] = cl[nl]);
    } else G = cl;
    return cl = G.ref, {
      $$typeof: M,
      type: g,
      key: Al,
      ref: cl !== void 0 ? cl : null,
      props: G
    };
  }
  return Du.Fragment = El, Du.jsx = P, Du.jsxs = P, Du;
}
var Tm;
function t0() {
  return Tm || (Tm = 1, mf.exports = l0()), mf.exports;
}
var f = t0(), hf = { exports: {} }, Q = {};
var xm;
function a0() {
  if (xm) return Q;
  xm = 1;
  var M = /* @__PURE__ */ Symbol.for("react.transitional.element"), El = /* @__PURE__ */ Symbol.for("react.portal"), P = /* @__PURE__ */ Symbol.for("react.fragment"), g = /* @__PURE__ */ Symbol.for("react.strict_mode"), cl = /* @__PURE__ */ Symbol.for("react.profiler"), G = /* @__PURE__ */ Symbol.for("react.consumer"), Al = /* @__PURE__ */ Symbol.for("react.context"), nl = /* @__PURE__ */ Symbol.for("react.forward_ref"), U = /* @__PURE__ */ Symbol.for("react.suspense"), p = /* @__PURE__ */ Symbol.for("react.memo"), X = /* @__PURE__ */ Symbol.for("react.lazy"), C = /* @__PURE__ */ Symbol.for("react.activity"), yl = Symbol.iterator;
  function jl(o) {
    return o === null || typeof o != "object" ? null : (o = yl && o[yl] || o["@@iterator"], typeof o == "function" ? o : null);
  }
  var Zl = {
    isMounted: function() {
      return !1;
    },
    enqueueForceUpdate: function() {
    },
    enqueueReplaceState: function() {
    },
    enqueueSetState: function() {
    }
  }, Cl = Object.assign, rt = {};
  function Yl(o, E, j) {
    this.props = o, this.context = E, this.refs = rt, this.updater = j || Zl;
  }
  Yl.prototype.isReactComponent = {}, Yl.prototype.setState = function(o, E) {
    if (typeof o != "object" && typeof o != "function" && o != null)
      throw Error(
        "takes an object of state variables to update or a function which returns an object of state variables."
      );
    this.updater.enqueueSetState(this, o, E, "setState");
  }, Yl.prototype.forceUpdate = function(o) {
    this.updater.enqueueForceUpdate(this, o, "forceUpdate");
  };
  function Ct() {
  }
  Ct.prototype = Yl.prototype;
  function Bl(o, E, j) {
    this.props = o, this.context = E, this.refs = rt, this.updater = j || Zl;
  }
  var Ql = Bl.prototype = new Ct();
  Ql.constructor = Bl, Cl(Ql, Yl.prototype), Ql.isPureReactComponent = !0;
  var Sl = Array.isArray;
  function bl() {
  }
  var w = { H: null, A: null, T: null, S: null }, Ll = Object.prototype.hasOwnProperty;
  function kl(o, E, j) {
    var O = j.ref;
    return {
      $$typeof: M,
      type: o,
      key: E,
      ref: O !== void 0 ? O : null,
      props: j
    };
  }
  function Zt(o, E) {
    return kl(o.type, E, o.props);
  }
  function gt(o) {
    return typeof o == "object" && o !== null && o.$$typeof === M;
  }
  function zl(o) {
    var E = { "=": "=0", ":": "=2" };
    return "$" + o.replace(/[=:]/g, function(j) {
      return E[j];
    });
  }
  var Rt = /\/+/g;
  function St(o, E) {
    return typeof o == "object" && o !== null && o.key != null ? zl("" + o.key) : E.toString(36);
  }
  function Pl(o) {
    switch (o.status) {
      case "fulfilled":
        return o.value;
      case "rejected":
        throw o.reason;
      default:
        switch (typeof o.status == "string" ? o.then(bl, bl) : (o.status = "pending", o.then(
          function(E) {
            o.status === "pending" && (o.status = "fulfilled", o.value = E);
          },
          function(E) {
            o.status === "pending" && (o.status = "rejected", o.reason = E);
          }
        )), o.status) {
          case "fulfilled":
            return o.value;
          case "rejected":
            throw o.reason;
        }
    }
    throw o;
  }
  function b(o, E, j, O, Z) {
    var J = typeof o;
    (J === "undefined" || J === "boolean") && (o = null);
    var al = !1;
    if (o === null) al = !0;
    else
      switch (J) {
        case "bigint":
        case "string":
        case "number":
          al = !0;
          break;
        case "object":
          switch (o.$$typeof) {
            case M:
            case El:
              al = !0;
              break;
            case X:
              return al = o._init, b(
                al(o._payload),
                E,
                j,
                O,
                Z
              );
          }
      }
    if (al)
      return Z = Z(o), al = O === "" ? "." + St(o, 0) : O, Sl(Z) ? (j = "", al != null && (j = al.replace(Rt, "$&/") + "/"), b(Z, E, j, "", function(qt) {
        return qt;
      })) : Z != null && (gt(Z) && (Z = Zt(
        Z,
        j + (Z.key == null || o && o.key === Z.key ? "" : ("" + Z.key).replace(
          Rt,
          "$&/"
        ) + "/") + al
      )), E.push(Z)), 1;
    al = 0;
    var Rl = O === "" ? "." : O + ":";
    if (Sl(o))
      for (var fl = 0; fl < o.length; fl++)
        O = o[fl], J = Rl + St(O, fl), al += b(
          O,
          E,
          j,
          J,
          Z
        );
    else if (fl = jl(o), typeof fl == "function")
      for (o = fl.call(o), fl = 0; !(O = o.next()).done; )
        O = O.value, J = Rl + St(O, fl++), al += b(
          O,
          E,
          j,
          J,
          Z
        );
    else if (J === "object") {
      if (typeof o.then == "function")
        return b(
          Pl(o),
          E,
          j,
          O,
          Z
        );
      throw E = String(o), Error(
        "Objects are not valid as a React child (found: " + (E === "[object Object]" ? "object with keys {" + Object.keys(o).join(", ") + "}" : E) + "). If you meant to render a collection of children, use an array instead."
      );
    }
    return al;
  }
  function _(o, E, j) {
    if (o == null) return o;
    var O = [], Z = 0;
    return b(o, O, "", "", function(J) {
      return E.call(j, J, Z++);
    }), O;
  }
  function Y(o) {
    if (o._status === -1) {
      var E = o._result;
      E = E(), E.then(
        function(j) {
          (o._status === 0 || o._status === -1) && (o._status = 1, o._result = j);
        },
        function(j) {
          (o._status === 0 || o._status === -1) && (o._status = 2, o._result = j);
        }
      ), o._status === -1 && (o._status = 0, o._result = E);
    }
    if (o._status === 1) return o._result.default;
    throw o._result;
  }
  var K = typeof reportError == "function" ? reportError : function(o) {
    if (typeof window == "object" && typeof window.ErrorEvent == "function") {
      var E = new window.ErrorEvent("error", {
        bubbles: !0,
        cancelable: !0,
        message: typeof o == "object" && o !== null && typeof o.message == "string" ? String(o.message) : String(o),
        error: o
      });
      if (!window.dispatchEvent(E)) return;
    } else if (typeof process == "object" && typeof process.emit == "function") {
      process.emit("uncaughtException", o);
      return;
    }
    console.error(o);
  }, ll = {
    map: _,
    forEach: function(o, E, j) {
      _(
        o,
        function() {
          E.apply(this, arguments);
        },
        j
      );
    },
    count: function(o) {
      var E = 0;
      return _(o, function() {
        E++;
      }), E;
    },
    toArray: function(o) {
      return _(o, function(E) {
        return E;
      }) || [];
    },
    only: function(o) {
      if (!gt(o))
        throw Error(
          "React.Children.only expected to receive a single React element child."
        );
      return o;
    }
  };
  return Q.Activity = C, Q.Children = ll, Q.Component = Yl, Q.Fragment = P, Q.Profiler = cl, Q.PureComponent = Bl, Q.StrictMode = g, Q.Suspense = U, Q.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = w, Q.__COMPILER_RUNTIME = {
    __proto__: null,
    c: function(o) {
      return w.H.useMemoCache(o);
    }
  }, Q.cache = function(o) {
    return function() {
      return o.apply(null, arguments);
    };
  }, Q.cacheSignal = function() {
    return null;
  }, Q.cloneElement = function(o, E, j) {
    if (o == null)
      throw Error(
        "The argument must be a React element, but you passed " + o + "."
      );
    var O = Cl({}, o.props), Z = o.key;
    if (E != null)
      for (J in E.key !== void 0 && (Z = "" + E.key), E)
        !Ll.call(E, J) || J === "key" || J === "__self" || J === "__source" || J === "ref" && E.ref === void 0 || (O[J] = E[J]);
    var J = arguments.length - 2;
    if (J === 1) O.children = j;
    else if (1 < J) {
      for (var al = Array(J), Rl = 0; Rl < J; Rl++)
        al[Rl] = arguments[Rl + 2];
      O.children = al;
    }
    return kl(o.type, Z, O);
  }, Q.createContext = function(o) {
    return o = {
      $$typeof: Al,
      _currentValue: o,
      _currentValue2: o,
      _threadCount: 0,
      Provider: null,
      Consumer: null
    }, o.Provider = o, o.Consumer = {
      $$typeof: G,
      _context: o
    }, o;
  }, Q.createElement = function(o, E, j) {
    var O, Z = {}, J = null;
    if (E != null)
      for (O in E.key !== void 0 && (J = "" + E.key), E)
        Ll.call(E, O) && O !== "key" && O !== "__self" && O !== "__source" && (Z[O] = E[O]);
    var al = arguments.length - 2;
    if (al === 1) Z.children = j;
    else if (1 < al) {
      for (var Rl = Array(al), fl = 0; fl < al; fl++)
        Rl[fl] = arguments[fl + 2];
      Z.children = Rl;
    }
    if (o && o.defaultProps)
      for (O in al = o.defaultProps, al)
        Z[O] === void 0 && (Z[O] = al[O]);
    return kl(o, J, Z);
  }, Q.createRef = function() {
    return { current: null };
  }, Q.forwardRef = function(o) {
    return { $$typeof: nl, render: o };
  }, Q.isValidElement = gt, Q.lazy = function(o) {
    return {
      $$typeof: X,
      _payload: { _status: -1, _result: o },
      _init: Y
    };
  }, Q.memo = function(o, E) {
    return {
      $$typeof: p,
      type: o,
      compare: E === void 0 ? null : E
    };
  }, Q.startTransition = function(o) {
    var E = w.T, j = {};
    w.T = j;
    try {
      var O = o(), Z = w.S;
      Z !== null && Z(j, O), typeof O == "object" && O !== null && typeof O.then == "function" && O.then(bl, K);
    } catch (J) {
      K(J);
    } finally {
      E !== null && j.types !== null && (E.types = j.types), w.T = E;
    }
  }, Q.unstable_useCacheRefresh = function() {
    return w.H.useCacheRefresh();
  }, Q.use = function(o) {
    return w.H.use(o);
  }, Q.useActionState = function(o, E, j) {
    return w.H.useActionState(o, E, j);
  }, Q.useCallback = function(o, E) {
    return w.H.useCallback(o, E);
  }, Q.useContext = function(o) {
    return w.H.useContext(o);
  }, Q.useDebugValue = function() {
  }, Q.useDeferredValue = function(o, E) {
    return w.H.useDeferredValue(o, E);
  }, Q.useEffect = function(o, E) {
    return w.H.useEffect(o, E);
  }, Q.useEffectEvent = function(o) {
    return w.H.useEffectEvent(o);
  }, Q.useId = function() {
    return w.H.useId();
  }, Q.useImperativeHandle = function(o, E, j) {
    return w.H.useImperativeHandle(o, E, j);
  }, Q.useInsertionEffect = function(o, E) {
    return w.H.useInsertionEffect(o, E);
  }, Q.useLayoutEffect = function(o, E) {
    return w.H.useLayoutEffect(o, E);
  }, Q.useMemo = function(o, E) {
    return w.H.useMemo(o, E);
  }, Q.useOptimistic = function(o, E) {
    return w.H.useOptimistic(o, E);
  }, Q.useReducer = function(o, E, j) {
    return w.H.useReducer(o, E, j);
  }, Q.useRef = function(o) {
    return w.H.useRef(o);
  }, Q.useState = function(o) {
    return w.H.useState(o);
  }, Q.useSyncExternalStore = function(o, E, j) {
    return w.H.useSyncExternalStore(
      o,
      E,
      j
    );
  }, Q.useTransition = function() {
    return w.H.useTransition();
  }, Q.version = "19.2.8", Q;
}
var jm;
function Ef() {
  return jm || (jm = 1, hf.exports = a0()), hf.exports;
}
var Hl = Ef(), vf = { exports: {} }, Uu = {}, yf = { exports: {} }, rf = {};
var _m;
function e0() {
  return _m || (_m = 1, (function(M) {
    function El(b, _) {
      var Y = b.length;
      b.push(_);
      l: for (; 0 < Y; ) {
        var K = Y - 1 >>> 1, ll = b[K];
        if (0 < cl(ll, _))
          b[K] = _, b[Y] = ll, Y = K;
        else break l;
      }
    }
    function P(b) {
      return b.length === 0 ? null : b[0];
    }
    function g(b) {
      if (b.length === 0) return null;
      var _ = b[0], Y = b.pop();
      if (Y !== _) {
        b[0] = Y;
        l: for (var K = 0, ll = b.length, o = ll >>> 1; K < o; ) {
          var E = 2 * (K + 1) - 1, j = b[E], O = E + 1, Z = b[O];
          if (0 > cl(j, Y))
            O < ll && 0 > cl(Z, j) ? (b[K] = Z, b[O] = Y, K = O) : (b[K] = j, b[E] = Y, K = E);
          else if (O < ll && 0 > cl(Z, Y))
            b[K] = Z, b[O] = Y, K = O;
          else break l;
        }
      }
      return _;
    }
    function cl(b, _) {
      var Y = b.sortIndex - _.sortIndex;
      return Y !== 0 ? Y : b.id - _.id;
    }
    if (M.unstable_now = void 0, typeof performance == "object" && typeof performance.now == "function") {
      var G = performance;
      M.unstable_now = function() {
        return G.now();
      };
    } else {
      var Al = Date, nl = Al.now();
      M.unstable_now = function() {
        return Al.now() - nl;
      };
    }
    var U = [], p = [], X = 1, C = null, yl = 3, jl = !1, Zl = !1, Cl = !1, rt = !1, Yl = typeof setTimeout == "function" ? setTimeout : null, Ct = typeof clearTimeout == "function" ? clearTimeout : null, Bl = typeof setImmediate < "u" ? setImmediate : null;
    function Ql(b) {
      for (var _ = P(p); _ !== null; ) {
        if (_.callback === null) g(p);
        else if (_.startTime <= b)
          g(p), _.sortIndex = _.expirationTime, El(U, _);
        else break;
        _ = P(p);
      }
    }
    function Sl(b) {
      if (Cl = !1, Ql(b), !Zl)
        if (P(U) !== null)
          Zl = !0, bl || (bl = !0, zl());
        else {
          var _ = P(p);
          _ !== null && Pl(Sl, _.startTime - b);
        }
    }
    var bl = !1, w = -1, Ll = 5, kl = -1;
    function Zt() {
      return rt ? !0 : !(M.unstable_now() - kl < Ll);
    }
    function gt() {
      if (rt = !1, bl) {
        var b = M.unstable_now();
        kl = b;
        var _ = !0;
        try {
          l: {
            Zl = !1, Cl && (Cl = !1, Ct(w), w = -1), jl = !0;
            var Y = yl;
            try {
              t: {
                for (Ql(b), C = P(U); C !== null && !(C.expirationTime > b && Zt()); ) {
                  var K = C.callback;
                  if (typeof K == "function") {
                    C.callback = null, yl = C.priorityLevel;
                    var ll = K(
                      C.expirationTime <= b
                    );
                    if (b = M.unstable_now(), typeof ll == "function") {
                      C.callback = ll, Ql(b), _ = !0;
                      break t;
                    }
                    C === P(U) && g(U), Ql(b);
                  } else g(U);
                  C = P(U);
                }
                if (C !== null) _ = !0;
                else {
                  var o = P(p);
                  o !== null && Pl(
                    Sl,
                    o.startTime - b
                  ), _ = !1;
                }
              }
              break l;
            } finally {
              C = null, yl = Y, jl = !1;
            }
            _ = void 0;
          }
        } finally {
          _ ? zl() : bl = !1;
        }
      }
    }
    var zl;
    if (typeof Bl == "function")
      zl = function() {
        Bl(gt);
      };
    else if (typeof MessageChannel < "u") {
      var Rt = new MessageChannel(), St = Rt.port2;
      Rt.port1.onmessage = gt, zl = function() {
        St.postMessage(null);
      };
    } else
      zl = function() {
        Yl(gt, 0);
      };
    function Pl(b, _) {
      w = Yl(function() {
        b(M.unstable_now());
      }, _);
    }
    M.unstable_IdlePriority = 5, M.unstable_ImmediatePriority = 1, M.unstable_LowPriority = 4, M.unstable_NormalPriority = 3, M.unstable_Profiling = null, M.unstable_UserBlockingPriority = 2, M.unstable_cancelCallback = function(b) {
      b.callback = null;
    }, M.unstable_forceFrameRate = function(b) {
      0 > b || 125 < b ? console.error(
        "forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"
      ) : Ll = 0 < b ? Math.floor(1e3 / b) : 5;
    }, M.unstable_getCurrentPriorityLevel = function() {
      return yl;
    }, M.unstable_next = function(b) {
      switch (yl) {
        case 1:
        case 2:
        case 3:
          var _ = 3;
          break;
        default:
          _ = yl;
      }
      var Y = yl;
      yl = _;
      try {
        return b();
      } finally {
        yl = Y;
      }
    }, M.unstable_requestPaint = function() {
      rt = !0;
    }, M.unstable_runWithPriority = function(b, _) {
      switch (b) {
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
          break;
        default:
          b = 3;
      }
      var Y = yl;
      yl = b;
      try {
        return _();
      } finally {
        yl = Y;
      }
    }, M.unstable_scheduleCallback = function(b, _, Y) {
      var K = M.unstable_now();
      switch (typeof Y == "object" && Y !== null ? (Y = Y.delay, Y = typeof Y == "number" && 0 < Y ? K + Y : K) : Y = K, b) {
        case 1:
          var ll = -1;
          break;
        case 2:
          ll = 250;
          break;
        case 5:
          ll = 1073741823;
          break;
        case 4:
          ll = 1e4;
          break;
        default:
          ll = 5e3;
      }
      return ll = Y + ll, b = {
        id: X++,
        callback: _,
        priorityLevel: b,
        startTime: Y,
        expirationTime: ll,
        sortIndex: -1
      }, Y > K ? (b.sortIndex = Y, El(p, b), P(U) === null && b === P(p) && (Cl ? (Ct(w), w = -1) : Cl = !0, Pl(Sl, Y - K))) : (b.sortIndex = ll, El(U, b), Zl || jl || (Zl = !0, bl || (bl = !0, zl()))), b;
    }, M.unstable_shouldYield = Zt, M.unstable_wrapCallback = function(b) {
      var _ = yl;
      return function() {
        var Y = yl;
        yl = _;
        try {
          return b.apply(this, arguments);
        } finally {
          yl = Y;
        }
      };
    };
  })(rf)), rf;
}
var Nm;
function u0() {
  return Nm || (Nm = 1, yf.exports = e0()), yf.exports;
}
var gf = { exports: {} }, Wl = {};
var Mm;
function n0() {
  if (Mm) return Wl;
  Mm = 1;
  var M = Ef();
  function El(U) {
    var p = "https://react.dev/errors/" + U;
    if (1 < arguments.length) {
      p += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var X = 2; X < arguments.length; X++)
        p += "&args[]=" + encodeURIComponent(arguments[X]);
    }
    return "Minified React error #" + U + "; visit " + p + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function P() {
  }
  var g = {
    d: {
      f: P,
      r: function() {
        throw Error(El(522));
      },
      D: P,
      C: P,
      L: P,
      m: P,
      X: P,
      S: P,
      M: P
    },
    p: 0,
    findDOMNode: null
  }, cl = /* @__PURE__ */ Symbol.for("react.portal");
  function G(U, p, X) {
    var C = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
    return {
      $$typeof: cl,
      key: C == null ? null : "" + C,
      children: U,
      containerInfo: p,
      implementation: X
    };
  }
  var Al = M.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
  function nl(U, p) {
    if (U === "font") return "";
    if (typeof p == "string")
      return p === "use-credentials" ? p : "";
  }
  return Wl.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = g, Wl.createPortal = function(U, p) {
    var X = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
    if (!p || p.nodeType !== 1 && p.nodeType !== 9 && p.nodeType !== 11)
      throw Error(El(299));
    return G(U, p, null, X);
  }, Wl.flushSync = function(U) {
    var p = Al.T, X = g.p;
    try {
      if (Al.T = null, g.p = 2, U) return U();
    } finally {
      Al.T = p, g.p = X, g.d.f();
    }
  }, Wl.preconnect = function(U, p) {
    typeof U == "string" && (p ? (p = p.crossOrigin, p = typeof p == "string" ? p === "use-credentials" ? p : "" : void 0) : p = null, g.d.C(U, p));
  }, Wl.prefetchDNS = function(U) {
    typeof U == "string" && g.d.D(U);
  }, Wl.preinit = function(U, p) {
    if (typeof U == "string" && p && typeof p.as == "string") {
      var X = p.as, C = nl(X, p.crossOrigin), yl = typeof p.integrity == "string" ? p.integrity : void 0, jl = typeof p.fetchPriority == "string" ? p.fetchPriority : void 0;
      X === "style" ? g.d.S(
        U,
        typeof p.precedence == "string" ? p.precedence : void 0,
        {
          crossOrigin: C,
          integrity: yl,
          fetchPriority: jl
        }
      ) : X === "script" && g.d.X(U, {
        crossOrigin: C,
        integrity: yl,
        fetchPriority: jl,
        nonce: typeof p.nonce == "string" ? p.nonce : void 0
      });
    }
  }, Wl.preinitModule = function(U, p) {
    if (typeof U == "string")
      if (typeof p == "object" && p !== null) {
        if (p.as == null || p.as === "script") {
          var X = nl(
            p.as,
            p.crossOrigin
          );
          g.d.M(U, {
            crossOrigin: X,
            integrity: typeof p.integrity == "string" ? p.integrity : void 0,
            nonce: typeof p.nonce == "string" ? p.nonce : void 0
          });
        }
      } else p == null && g.d.M(U);
  }, Wl.preload = function(U, p) {
    if (typeof U == "string" && typeof p == "object" && p !== null && typeof p.as == "string") {
      var X = p.as, C = nl(X, p.crossOrigin);
      g.d.L(U, X, {
        crossOrigin: C,
        integrity: typeof p.integrity == "string" ? p.integrity : void 0,
        nonce: typeof p.nonce == "string" ? p.nonce : void 0,
        type: typeof p.type == "string" ? p.type : void 0,
        fetchPriority: typeof p.fetchPriority == "string" ? p.fetchPriority : void 0,
        referrerPolicy: typeof p.referrerPolicy == "string" ? p.referrerPolicy : void 0,
        imageSrcSet: typeof p.imageSrcSet == "string" ? p.imageSrcSet : void 0,
        imageSizes: typeof p.imageSizes == "string" ? p.imageSizes : void 0,
        media: typeof p.media == "string" ? p.media : void 0
      });
    }
  }, Wl.preloadModule = function(U, p) {
    if (typeof U == "string")
      if (p) {
        var X = nl(p.as, p.crossOrigin);
        g.d.m(U, {
          as: typeof p.as == "string" && p.as !== "script" ? p.as : void 0,
          crossOrigin: X,
          integrity: typeof p.integrity == "string" ? p.integrity : void 0
        });
      } else g.d.m(U);
  }, Wl.requestFormReset = function(U) {
    g.d.r(U);
  }, Wl.unstable_batchedUpdates = function(U, p) {
    return U(p);
  }, Wl.useFormState = function(U, p, X) {
    return Al.H.useFormState(U, p, X);
  }, Wl.useFormStatus = function() {
    return Al.H.useHostTransitionStatus();
  }, Wl.version = "19.2.8", Wl;
}
var Om;
function i0() {
  if (Om) return gf.exports;
  Om = 1;
  function M() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function"))
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(M);
      } catch (El) {
        console.error(El);
      }
  }
  return M(), gf.exports = n0(), gf.exports;
}
var Dm;
function c0() {
  if (Dm) return Uu;
  Dm = 1;
  var M = u0(), El = Ef(), P = i0();
  function g(l) {
    var t = "https://react.dev/errors/" + l;
    if (1 < arguments.length) {
      t += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var a = 2; a < arguments.length; a++)
        t += "&args[]=" + encodeURIComponent(arguments[a]);
    }
    return "Minified React error #" + l + "; visit " + t + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function cl(l) {
    return !(!l || l.nodeType !== 1 && l.nodeType !== 9 && l.nodeType !== 11);
  }
  function G(l) {
    var t = l, a = l;
    if (l.alternate) for (; t.return; ) t = t.return;
    else {
      l = t;
      do
        t = l, (t.flags & 4098) !== 0 && (a = t.return), l = t.return;
      while (l);
    }
    return t.tag === 3 ? a : null;
  }
  function Al(l) {
    if (l.tag === 13) {
      var t = l.memoizedState;
      if (t === null && (l = l.alternate, l !== null && (t = l.memoizedState)), t !== null) return t.dehydrated;
    }
    return null;
  }
  function nl(l) {
    if (l.tag === 31) {
      var t = l.memoizedState;
      if (t === null && (l = l.alternate, l !== null && (t = l.memoizedState)), t !== null) return t.dehydrated;
    }
    return null;
  }
  function U(l) {
    if (G(l) !== l)
      throw Error(g(188));
  }
  function p(l) {
    var t = l.alternate;
    if (!t) {
      if (t = G(l), t === null) throw Error(g(188));
      return t !== l ? null : l;
    }
    for (var a = l, e = t; ; ) {
      var u = a.return;
      if (u === null) break;
      var n = u.alternate;
      if (n === null) {
        if (e = u.return, e !== null) {
          a = e;
          continue;
        }
        break;
      }
      if (u.child === n.child) {
        for (n = u.child; n; ) {
          if (n === a) return U(u), l;
          if (n === e) return U(u), t;
          n = n.sibling;
        }
        throw Error(g(188));
      }
      if (a.return !== e.return) a = u, e = n;
      else {
        for (var i = !1, c = u.child; c; ) {
          if (c === a) {
            i = !0, a = u, e = n;
            break;
          }
          if (c === e) {
            i = !0, e = u, a = n;
            break;
          }
          c = c.sibling;
        }
        if (!i) {
          for (c = n.child; c; ) {
            if (c === a) {
              i = !0, a = n, e = u;
              break;
            }
            if (c === e) {
              i = !0, e = n, a = u;
              break;
            }
            c = c.sibling;
          }
          if (!i) throw Error(g(189));
        }
      }
      if (a.alternate !== e) throw Error(g(190));
    }
    if (a.tag !== 3) throw Error(g(188));
    return a.stateNode.current === a ? l : t;
  }
  function X(l) {
    var t = l.tag;
    if (t === 5 || t === 26 || t === 27 || t === 6) return l;
    for (l = l.child; l !== null; ) {
      if (t = X(l), t !== null) return t;
      l = l.sibling;
    }
    return null;
  }
  var C = Object.assign, yl = /* @__PURE__ */ Symbol.for("react.element"), jl = /* @__PURE__ */ Symbol.for("react.transitional.element"), Zl = /* @__PURE__ */ Symbol.for("react.portal"), Cl = /* @__PURE__ */ Symbol.for("react.fragment"), rt = /* @__PURE__ */ Symbol.for("react.strict_mode"), Yl = /* @__PURE__ */ Symbol.for("react.profiler"), Ct = /* @__PURE__ */ Symbol.for("react.consumer"), Bl = /* @__PURE__ */ Symbol.for("react.context"), Ql = /* @__PURE__ */ Symbol.for("react.forward_ref"), Sl = /* @__PURE__ */ Symbol.for("react.suspense"), bl = /* @__PURE__ */ Symbol.for("react.suspense_list"), w = /* @__PURE__ */ Symbol.for("react.memo"), Ll = /* @__PURE__ */ Symbol.for("react.lazy"), kl = /* @__PURE__ */ Symbol.for("react.activity"), Zt = /* @__PURE__ */ Symbol.for("react.memo_cache_sentinel"), gt = Symbol.iterator;
  function zl(l) {
    return l === null || typeof l != "object" ? null : (l = gt && l[gt] || l["@@iterator"], typeof l == "function" ? l : null);
  }
  var Rt = /* @__PURE__ */ Symbol.for("react.client.reference");
  function St(l) {
    if (l == null) return null;
    if (typeof l == "function")
      return l.$$typeof === Rt ? null : l.displayName || l.name || null;
    if (typeof l == "string") return l;
    switch (l) {
      case Cl:
        return "Fragment";
      case Yl:
        return "Profiler";
      case rt:
        return "StrictMode";
      case Sl:
        return "Suspense";
      case bl:
        return "SuspenseList";
      case kl:
        return "Activity";
    }
    if (typeof l == "object")
      switch (l.$$typeof) {
        case Zl:
          return "Portal";
        case Bl:
          return l.displayName || "Context";
        case Ct:
          return (l._context.displayName || "Context") + ".Consumer";
        case Ql:
          var t = l.render;
          return l = l.displayName, l || (l = t.displayName || t.name || "", l = l !== "" ? "ForwardRef(" + l + ")" : "ForwardRef"), l;
        case w:
          return t = l.displayName || null, t !== null ? t : St(l.type) || "Memo";
        case Ll:
          t = l._payload, l = l._init;
          try {
            return St(l(t));
          } catch {
          }
      }
    return null;
  }
  var Pl = Array.isArray, b = El.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, _ = P.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, Y = {
    pending: !1,
    data: null,
    method: null,
    action: null
  }, K = [], ll = -1;
  function o(l) {
    return { current: l };
  }
  function E(l) {
    0 > ll || (l.current = K[ll], K[ll] = null, ll--);
  }
  function j(l, t) {
    ll++, K[ll] = l.current, l.current = t;
  }
  var O = o(null), Z = o(null), J = o(null), al = o(null);
  function Rl(l, t) {
    switch (j(J, t), j(Z, l), j(O, null), t.nodeType) {
      case 9:
      case 11:
        l = (l = t.documentElement) && (l = l.namespaceURI) ? Jo(l) : 0;
        break;
      default:
        if (l = t.tagName, t = t.namespaceURI)
          t = Jo(t), l = wo(t, l);
        else
          switch (l) {
            case "svg":
              l = 1;
              break;
            case "math":
              l = 2;
              break;
            default:
              l = 0;
          }
    }
    E(O), j(O, l);
  }
  function fl() {
    E(O), E(Z), E(J);
  }
  function qt(l) {
    l.memoizedState !== null && j(al, l);
    var t = O.current, a = wo(t, l.type);
    t !== a && (j(Z, l), j(O, a));
  }
  function bt(l) {
    Z.current === l && (E(O), E(Z)), al.current === l && (E(al), _u._currentValue = Y);
  }
  var Ua, Hu;
  function Ot(l) {
    if (Ua === void 0)
      try {
        throw Error();
      } catch (a) {
        var t = a.stack.trim().match(/\n( *(at )?)/);
        Ua = t && t[1] || "", Hu = -1 < a.stack.indexOf(`
    at`) ? " (<anonymous>)" : -1 < a.stack.indexOf("@") ? "@unknown:0:0" : "";
      }
    return `
` + Ua + l + Hu;
  }
  var Ia = !1;
  function d(l, t) {
    if (!l || Ia) return "";
    Ia = !0;
    var a = Error.prepareStackTrace;
    Error.prepareStackTrace = void 0;
    try {
      var e = {
        DetermineComponentFrameRoot: function() {
          try {
            if (t) {
              var T = function() {
                throw Error();
              };
              if (Object.defineProperty(T.prototype, "props", {
                set: function() {
                  throw Error();
                }
              }), typeof Reflect == "object" && Reflect.construct) {
                try {
                  Reflect.construct(T, []);
                } catch (S) {
                  var r = S;
                }
                Reflect.construct(l, [], T);
              } else {
                try {
                  T.call();
                } catch (S) {
                  r = S;
                }
                l.call(T.prototype);
              }
            } else {
              try {
                throw Error();
              } catch (S) {
                r = S;
              }
              (T = l()) && typeof T.catch == "function" && T.catch(function() {
              });
            }
          } catch (S) {
            if (S && r && typeof S.stack == "string")
              return [S.stack, r.stack];
          }
          return [null, null];
        }
      };
      e.DetermineComponentFrameRoot.displayName = "DetermineComponentFrameRoot";
      var u = Object.getOwnPropertyDescriptor(
        e.DetermineComponentFrameRoot,
        "name"
      );
      u && u.configurable && Object.defineProperty(
        e.DetermineComponentFrameRoot,
        "name",
        { value: "DetermineComponentFrameRoot" }
      );
      var n = e.DetermineComponentFrameRoot(), i = n[0], c = n[1];
      if (i && c) {
        var s = i.split(`
`), y = c.split(`
`);
        for (u = e = 0; e < s.length && !s[e].includes("DetermineComponentFrameRoot"); )
          e++;
        for (; u < y.length && !y[u].includes(
          "DetermineComponentFrameRoot"
        ); )
          u++;
        if (e === s.length || u === y.length)
          for (e = s.length - 1, u = y.length - 1; 1 <= e && 0 <= u && s[e] !== y[u]; )
            u--;
        for (; 1 <= e && 0 <= u; e--, u--)
          if (s[e] !== y[u]) {
            if (e !== 1 || u !== 1)
              do
                if (e--, u--, 0 > u || s[e] !== y[u]) {
                  var z = `
` + s[e].replace(" at new ", " at ");
                  return l.displayName && z.includes("<anonymous>") && (z = z.replace("<anonymous>", l.displayName)), z;
                }
              while (1 <= e && 0 <= u);
            break;
          }
      }
    } finally {
      Ia = !1, Error.prepareStackTrace = a;
    }
    return (a = l ? l.displayName || l.name : "") ? Ot(a) : "";
  }
  function x(l, t) {
    switch (l.tag) {
      case 26:
      case 27:
      case 5:
        return Ot(l.type);
      case 16:
        return Ot("Lazy");
      case 13:
        return l.child !== t && t !== null ? Ot("Suspense Fallback") : Ot("Suspense");
      case 19:
        return Ot("SuspenseList");
      case 0:
      case 15:
        return d(l.type, !1);
      case 11:
        return d(l.type.render, !1);
      case 1:
        return d(l.type, !0);
      case 31:
        return Ot("Activity");
      default:
        return "";
    }
  }
  function N(l) {
    try {
      var t = "", a = null;
      do
        t += x(l, a), a = l, l = l.return;
      while (l);
      return t;
    } catch (e) {
      return `
Error generating stack: ` + e.message + `
` + e.stack;
    }
  }
  var R = Object.prototype.hasOwnProperty, tl = M.unstable_scheduleCallback, Ha = M.unstable_cancelCallback, Ye = M.unstable_shouldYield, Be = M.unstable_requestPaint, Fl = M.unstable_now, Cm = M.unstable_getCurrentPriorityLevel, Af = M.unstable_ImmediatePriority, Tf = M.unstable_UserBlockingPriority, Cu = M.unstable_NormalPriority, Rm = M.unstable_LowPriority, xf = M.unstable_IdlePriority, qm = M.log, Ym = M.unstable_setDisableYieldValue, Ge = null, ct = null;
  function ia(l) {
    if (typeof qm == "function" && Ym(l), ct && typeof ct.setStrictMode == "function")
      try {
        ct.setStrictMode(Ge, l);
      } catch {
      }
  }
  var ft = Math.clz32 ? Math.clz32 : Xm, Bm = Math.log, Gm = Math.LN2;
  function Xm(l) {
    return l >>>= 0, l === 0 ? 32 : 31 - (Bm(l) / Gm | 0) | 0;
  }
  var Ru = 256, qu = 262144, Yu = 4194304;
  function Ca(l) {
    var t = l & 42;
    if (t !== 0) return t;
    switch (l & -l) {
      case 1:
        return 1;
      case 2:
        return 2;
      case 4:
        return 4;
      case 8:
        return 8;
      case 16:
        return 16;
      case 32:
        return 32;
      case 64:
        return 64;
      case 128:
        return 128;
      case 256:
      case 512:
      case 1024:
      case 2048:
      case 4096:
      case 8192:
      case 16384:
      case 32768:
      case 65536:
      case 131072:
        return l & 261888;
      case 262144:
      case 524288:
      case 1048576:
      case 2097152:
        return l & 3932160;
      case 4194304:
      case 8388608:
      case 16777216:
      case 33554432:
        return l & 62914560;
      case 67108864:
        return 67108864;
      case 134217728:
        return 134217728;
      case 268435456:
        return 268435456;
      case 536870912:
        return 536870912;
      case 1073741824:
        return 0;
      default:
        return l;
    }
  }
  function Bu(l, t, a) {
    var e = l.pendingLanes;
    if (e === 0) return 0;
    var u = 0, n = l.suspendedLanes, i = l.pingedLanes;
    l = l.warmLanes;
    var c = e & 134217727;
    return c !== 0 ? (e = c & ~n, e !== 0 ? u = Ca(e) : (i &= c, i !== 0 ? u = Ca(i) : a || (a = c & ~l, a !== 0 && (u = Ca(a))))) : (c = e & ~n, c !== 0 ? u = Ca(c) : i !== 0 ? u = Ca(i) : a || (a = e & ~l, a !== 0 && (u = Ca(a)))), u === 0 ? 0 : t !== 0 && t !== u && (t & n) === 0 && (n = u & -u, a = t & -t, n >= a || n === 32 && (a & 4194048) !== 0) ? t : u;
  }
  function Xe(l, t) {
    return (l.pendingLanes & ~(l.suspendedLanes & ~l.pingedLanes) & t) === 0;
  }
  function Zm(l, t) {
    switch (l) {
      case 1:
      case 2:
      case 4:
      case 8:
      case 64:
        return t + 250;
      case 16:
      case 32:
      case 128:
      case 256:
      case 512:
      case 1024:
      case 2048:
      case 4096:
      case 8192:
      case 16384:
      case 32768:
      case 65536:
      case 131072:
      case 262144:
      case 524288:
      case 1048576:
      case 2097152:
        return t + 5e3;
      case 4194304:
      case 8388608:
      case 16777216:
      case 33554432:
        return -1;
      case 67108864:
      case 134217728:
      case 268435456:
      case 536870912:
      case 1073741824:
        return -1;
      default:
        return -1;
    }
  }
  function jf() {
    var l = Yu;
    return Yu <<= 1, (Yu & 62914560) === 0 && (Yu = 4194304), l;
  }
  function Pn(l) {
    for (var t = [], a = 0; 31 > a; a++) t.push(l);
    return t;
  }
  function Ze(l, t) {
    l.pendingLanes |= t, t !== 268435456 && (l.suspendedLanes = 0, l.pingedLanes = 0, l.warmLanes = 0);
  }
  function Qm(l, t, a, e, u, n) {
    var i = l.pendingLanes;
    l.pendingLanes = a, l.suspendedLanes = 0, l.pingedLanes = 0, l.warmLanes = 0, l.expiredLanes &= a, l.entangledLanes &= a, l.errorRecoveryDisabledLanes &= a, l.shellSuspendCounter = 0;
    var c = l.entanglements, s = l.expirationTimes, y = l.hiddenUpdates;
    for (a = i & ~a; 0 < a; ) {
      var z = 31 - ft(a), T = 1 << z;
      c[z] = 0, s[z] = -1;
      var r = y[z];
      if (r !== null)
        for (y[z] = null, z = 0; z < r.length; z++) {
          var S = r[z];
          S !== null && (S.lane &= -536870913);
        }
      a &= ~T;
    }
    e !== 0 && _f(l, e, 0), n !== 0 && u === 0 && l.tag !== 0 && (l.suspendedLanes |= n & ~(i & ~t));
  }
  function _f(l, t, a) {
    l.pendingLanes |= t, l.suspendedLanes &= ~t;
    var e = 31 - ft(t);
    l.entangledLanes |= t, l.entanglements[e] = l.entanglements[e] | 1073741824 | a & 261930;
  }
  function Nf(l, t) {
    var a = l.entangledLanes |= t;
    for (l = l.entanglements; a; ) {
      var e = 31 - ft(a), u = 1 << e;
      u & t | l[e] & t && (l[e] |= t), a &= ~u;
    }
  }
  function Mf(l, t) {
    var a = t & -t;
    return a = (a & 42) !== 0 ? 1 : li(a), (a & (l.suspendedLanes | t)) !== 0 ? 0 : a;
  }
  function li(l) {
    switch (l) {
      case 2:
        l = 1;
        break;
      case 8:
        l = 4;
        break;
      case 32:
        l = 16;
        break;
      case 256:
      case 512:
      case 1024:
      case 2048:
      case 4096:
      case 8192:
      case 16384:
      case 32768:
      case 65536:
      case 131072:
      case 262144:
      case 524288:
      case 1048576:
      case 2097152:
      case 4194304:
      case 8388608:
      case 16777216:
      case 33554432:
        l = 128;
        break;
      case 268435456:
        l = 134217728;
        break;
      default:
        l = 0;
    }
    return l;
  }
  function ti(l) {
    return l &= -l, 2 < l ? 8 < l ? (l & 134217727) !== 0 ? 32 : 268435456 : 8 : 2;
  }
  function Of() {
    var l = _.p;
    return l !== 0 ? l : (l = window.event, l === void 0 ? 32 : rm(l.type));
  }
  function Df(l, t) {
    var a = _.p;
    try {
      return _.p = l, t();
    } finally {
      _.p = a;
    }
  }
  var ca = Math.random().toString(36).slice(2), Vl = "__reactFiber$" + ca, lt = "__reactProps$" + ca, Pa = "__reactContainer$" + ca, ai = "__reactEvents$" + ca, Lm = "__reactListeners$" + ca, Vm = "__reactHandles$" + ca, Uf = "__reactResources$" + ca, Qe = "__reactMarker$" + ca;
  function ei(l) {
    delete l[Vl], delete l[lt], delete l[ai], delete l[Lm], delete l[Vm];
  }
  function le(l) {
    var t = l[Vl];
    if (t) return t;
    for (var a = l.parentNode; a; ) {
      if (t = a[Pa] || a[Vl]) {
        if (a = t.alternate, t.child !== null || a !== null && a.child !== null)
          for (l = lm(l); l !== null; ) {
            if (a = l[Vl]) return a;
            l = lm(l);
          }
        return t;
      }
      l = a, a = l.parentNode;
    }
    return null;
  }
  function te(l) {
    if (l = l[Vl] || l[Pa]) {
      var t = l.tag;
      if (t === 5 || t === 6 || t === 13 || t === 31 || t === 26 || t === 27 || t === 3)
        return l;
    }
    return null;
  }
  function Le(l) {
    var t = l.tag;
    if (t === 5 || t === 26 || t === 27 || t === 6) return l.stateNode;
    throw Error(g(33));
  }
  function ae(l) {
    var t = l[Uf];
    return t || (t = l[Uf] = { hoistableStyles: /* @__PURE__ */ new Map(), hoistableScripts: /* @__PURE__ */ new Map() }), t;
  }
  function Gl(l) {
    l[Qe] = !0;
  }
  var Hf = /* @__PURE__ */ new Set(), Cf = {};
  function Ra(l, t) {
    ee(l, t), ee(l + "Capture", t);
  }
  function ee(l, t) {
    for (Cf[l] = t, l = 0; l < t.length; l++)
      Hf.add(t[l]);
  }
  var Km = RegExp(
    "^[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"
  ), Rf = {}, qf = {};
  function Jm(l) {
    return R.call(qf, l) ? !0 : R.call(Rf, l) ? !1 : Km.test(l) ? qf[l] = !0 : (Rf[l] = !0, !1);
  }
  function Gu(l, t, a) {
    if (Jm(t))
      if (a === null) l.removeAttribute(t);
      else {
        switch (typeof a) {
          case "undefined":
          case "function":
          case "symbol":
            l.removeAttribute(t);
            return;
          case "boolean":
            var e = t.toLowerCase().slice(0, 5);
            if (e !== "data-" && e !== "aria-") {
              l.removeAttribute(t);
              return;
            }
        }
        l.setAttribute(t, "" + a);
      }
  }
  function Xu(l, t, a) {
    if (a === null) l.removeAttribute(t);
    else {
      switch (typeof a) {
        case "undefined":
        case "function":
        case "symbol":
        case "boolean":
          l.removeAttribute(t);
          return;
      }
      l.setAttribute(t, "" + a);
    }
  }
  function Qt(l, t, a, e) {
    if (e === null) l.removeAttribute(a);
    else {
      switch (typeof e) {
        case "undefined":
        case "function":
        case "symbol":
        case "boolean":
          l.removeAttribute(a);
          return;
      }
      l.setAttributeNS(t, a, "" + e);
    }
  }
  function pt(l) {
    switch (typeof l) {
      case "bigint":
      case "boolean":
      case "number":
      case "string":
      case "undefined":
        return l;
      case "object":
        return l;
      default:
        return "";
    }
  }
  function Yf(l) {
    var t = l.type;
    return (l = l.nodeName) && l.toLowerCase() === "input" && (t === "checkbox" || t === "radio");
  }
  function wm(l, t, a) {
    var e = Object.getOwnPropertyDescriptor(
      l.constructor.prototype,
      t
    );
    if (!l.hasOwnProperty(t) && typeof e < "u" && typeof e.get == "function" && typeof e.set == "function") {
      var u = e.get, n = e.set;
      return Object.defineProperty(l, t, {
        configurable: !0,
        get: function() {
          return u.call(this);
        },
        set: function(i) {
          a = "" + i, n.call(this, i);
        }
      }), Object.defineProperty(l, t, {
        enumerable: e.enumerable
      }), {
        getValue: function() {
          return a;
        },
        setValue: function(i) {
          a = "" + i;
        },
        stopTracking: function() {
          l._valueTracker = null, delete l[t];
        }
      };
    }
  }
  function ui(l) {
    if (!l._valueTracker) {
      var t = Yf(l) ? "checked" : "value";
      l._valueTracker = wm(
        l,
        t,
        "" + l[t]
      );
    }
  }
  function Bf(l) {
    if (!l) return !1;
    var t = l._valueTracker;
    if (!t) return !0;
    var a = t.getValue(), e = "";
    return l && (e = Yf(l) ? l.checked ? "true" : "false" : l.value), l = e, l !== a ? (t.setValue(l), !0) : !1;
  }
  function Zu(l) {
    if (l = l || (typeof document < "u" ? document : void 0), typeof l > "u") return null;
    try {
      return l.activeElement || l.body;
    } catch {
      return l.body;
    }
  }
  var $m = /[\n"\\]/g;
  function zt(l) {
    return l.replace(
      $m,
      function(t) {
        return "\\" + t.charCodeAt(0).toString(16) + " ";
      }
    );
  }
  function ni(l, t, a, e, u, n, i, c) {
    l.name = "", i != null && typeof i != "function" && typeof i != "symbol" && typeof i != "boolean" ? l.type = i : l.removeAttribute("type"), t != null ? i === "number" ? (t === 0 && l.value === "" || l.value != t) && (l.value = "" + pt(t)) : l.value !== "" + pt(t) && (l.value = "" + pt(t)) : i !== "submit" && i !== "reset" || l.removeAttribute("value"), t != null ? ii(l, i, pt(t)) : a != null ? ii(l, i, pt(a)) : e != null && l.removeAttribute("value"), u == null && n != null && (l.defaultChecked = !!n), u != null && (l.checked = u && typeof u != "function" && typeof u != "symbol"), c != null && typeof c != "function" && typeof c != "symbol" && typeof c != "boolean" ? l.name = "" + pt(c) : l.removeAttribute("name");
  }
  function Gf(l, t, a, e, u, n, i, c) {
    if (n != null && typeof n != "function" && typeof n != "symbol" && typeof n != "boolean" && (l.type = n), t != null || a != null) {
      if (!(n !== "submit" && n !== "reset" || t != null)) {
        ui(l);
        return;
      }
      a = a != null ? "" + pt(a) : "", t = t != null ? "" + pt(t) : a, c || t === l.value || (l.value = t), l.defaultValue = t;
    }
    e = e ?? u, e = typeof e != "function" && typeof e != "symbol" && !!e, l.checked = c ? l.checked : !!e, l.defaultChecked = !!e, i != null && typeof i != "function" && typeof i != "symbol" && typeof i != "boolean" && (l.name = i), ui(l);
  }
  function ii(l, t, a) {
    t === "number" && Zu(l.ownerDocument) === l || l.defaultValue === "" + a || (l.defaultValue = "" + a);
  }
  function ue(l, t, a, e) {
    if (l = l.options, t) {
      t = {};
      for (var u = 0; u < a.length; u++)
        t["$" + a[u]] = !0;
      for (a = 0; a < l.length; a++)
        u = t.hasOwnProperty("$" + l[a].value), l[a].selected !== u && (l[a].selected = u), u && e && (l[a].defaultSelected = !0);
    } else {
      for (a = "" + pt(a), t = null, u = 0; u < l.length; u++) {
        if (l[u].value === a) {
          l[u].selected = !0, e && (l[u].defaultSelected = !0);
          return;
        }
        t !== null || l[u].disabled || (t = l[u]);
      }
      t !== null && (t.selected = !0);
    }
  }
  function Xf(l, t, a) {
    if (t != null && (t = "" + pt(t), t !== l.value && (l.value = t), a == null)) {
      l.defaultValue !== t && (l.defaultValue = t);
      return;
    }
    l.defaultValue = a != null ? "" + pt(a) : "";
  }
  function Zf(l, t, a, e) {
    if (t == null) {
      if (e != null) {
        if (a != null) throw Error(g(92));
        if (Pl(e)) {
          if (1 < e.length) throw Error(g(93));
          e = e[0];
        }
        a = e;
      }
      a == null && (a = ""), t = a;
    }
    a = pt(t), l.defaultValue = a, e = l.textContent, e === a && e !== "" && e !== null && (l.value = e), ui(l);
  }
  function ne(l, t) {
    if (t) {
      var a = l.firstChild;
      if (a && a === l.lastChild && a.nodeType === 3) {
        a.nodeValue = t;
        return;
      }
    }
    l.textContent = t;
  }
  var Wm = new Set(
    "animationIterationCount aspectRatio borderImageOutset borderImageSlice borderImageWidth boxFlex boxFlexGroup boxOrdinalGroup columnCount columns flex flexGrow flexPositive flexShrink flexNegative flexOrder gridArea gridRow gridRowEnd gridRowSpan gridRowStart gridColumn gridColumnEnd gridColumnSpan gridColumnStart fontWeight lineClamp lineHeight opacity order orphans scale tabSize widows zIndex zoom fillOpacity floodOpacity stopOpacity strokeDasharray strokeDashoffset strokeMiterlimit strokeOpacity strokeWidth MozAnimationIterationCount MozBoxFlex MozBoxFlexGroup MozLineClamp msAnimationIterationCount msFlex msZoom msFlexGrow msFlexNegative msFlexOrder msFlexPositive msFlexShrink msGridColumn msGridColumnSpan msGridRow msGridRowSpan WebkitAnimationIterationCount WebkitBoxFlex WebKitBoxFlexGroup WebkitBoxOrdinalGroup WebkitColumnCount WebkitColumns WebkitFlex WebkitFlexGrow WebkitFlexPositive WebkitFlexShrink WebkitLineClamp".split(
      " "
    )
  );
  function Qf(l, t, a) {
    var e = t.indexOf("--") === 0;
    a == null || typeof a == "boolean" || a === "" ? e ? l.setProperty(t, "") : t === "float" ? l.cssFloat = "" : l[t] = "" : e ? l.setProperty(t, a) : typeof a != "number" || a === 0 || Wm.has(t) ? t === "float" ? l.cssFloat = a : l[t] = ("" + a).trim() : l[t] = a + "px";
  }
  function Lf(l, t, a) {
    if (t != null && typeof t != "object")
      throw Error(g(62));
    if (l = l.style, a != null) {
      for (var e in a)
        !a.hasOwnProperty(e) || t != null && t.hasOwnProperty(e) || (e.indexOf("--") === 0 ? l.setProperty(e, "") : e === "float" ? l.cssFloat = "" : l[e] = "");
      for (var u in t)
        e = t[u], t.hasOwnProperty(u) && a[u] !== e && Qf(l, u, e);
    } else
      for (var n in t)
        t.hasOwnProperty(n) && Qf(l, n, t[n]);
  }
  function ci(l) {
    if (l.indexOf("-") === -1) return !1;
    switch (l) {
      case "annotation-xml":
      case "color-profile":
      case "font-face":
      case "font-face-src":
      case "font-face-uri":
      case "font-face-format":
      case "font-face-name":
      case "missing-glyph":
        return !1;
      default:
        return !0;
    }
  }
  var km = /* @__PURE__ */ new Map([
    ["acceptCharset", "accept-charset"],
    ["htmlFor", "for"],
    ["httpEquiv", "http-equiv"],
    ["crossOrigin", "crossorigin"],
    ["accentHeight", "accent-height"],
    ["alignmentBaseline", "alignment-baseline"],
    ["arabicForm", "arabic-form"],
    ["baselineShift", "baseline-shift"],
    ["capHeight", "cap-height"],
    ["clipPath", "clip-path"],
    ["clipRule", "clip-rule"],
    ["colorInterpolation", "color-interpolation"],
    ["colorInterpolationFilters", "color-interpolation-filters"],
    ["colorProfile", "color-profile"],
    ["colorRendering", "color-rendering"],
    ["dominantBaseline", "dominant-baseline"],
    ["enableBackground", "enable-background"],
    ["fillOpacity", "fill-opacity"],
    ["fillRule", "fill-rule"],
    ["floodColor", "flood-color"],
    ["floodOpacity", "flood-opacity"],
    ["fontFamily", "font-family"],
    ["fontSize", "font-size"],
    ["fontSizeAdjust", "font-size-adjust"],
    ["fontStretch", "font-stretch"],
    ["fontStyle", "font-style"],
    ["fontVariant", "font-variant"],
    ["fontWeight", "font-weight"],
    ["glyphName", "glyph-name"],
    ["glyphOrientationHorizontal", "glyph-orientation-horizontal"],
    ["glyphOrientationVertical", "glyph-orientation-vertical"],
    ["horizAdvX", "horiz-adv-x"],
    ["horizOriginX", "horiz-origin-x"],
    ["imageRendering", "image-rendering"],
    ["letterSpacing", "letter-spacing"],
    ["lightingColor", "lighting-color"],
    ["markerEnd", "marker-end"],
    ["markerMid", "marker-mid"],
    ["markerStart", "marker-start"],
    ["overlinePosition", "overline-position"],
    ["overlineThickness", "overline-thickness"],
    ["paintOrder", "paint-order"],
    ["panose-1", "panose-1"],
    ["pointerEvents", "pointer-events"],
    ["renderingIntent", "rendering-intent"],
    ["shapeRendering", "shape-rendering"],
    ["stopColor", "stop-color"],
    ["stopOpacity", "stop-opacity"],
    ["strikethroughPosition", "strikethrough-position"],
    ["strikethroughThickness", "strikethrough-thickness"],
    ["strokeDasharray", "stroke-dasharray"],
    ["strokeDashoffset", "stroke-dashoffset"],
    ["strokeLinecap", "stroke-linecap"],
    ["strokeLinejoin", "stroke-linejoin"],
    ["strokeMiterlimit", "stroke-miterlimit"],
    ["strokeOpacity", "stroke-opacity"],
    ["strokeWidth", "stroke-width"],
    ["textAnchor", "text-anchor"],
    ["textDecoration", "text-decoration"],
    ["textRendering", "text-rendering"],
    ["transformOrigin", "transform-origin"],
    ["underlinePosition", "underline-position"],
    ["underlineThickness", "underline-thickness"],
    ["unicodeBidi", "unicode-bidi"],
    ["unicodeRange", "unicode-range"],
    ["unitsPerEm", "units-per-em"],
    ["vAlphabetic", "v-alphabetic"],
    ["vHanging", "v-hanging"],
    ["vIdeographic", "v-ideographic"],
    ["vMathematical", "v-mathematical"],
    ["vectorEffect", "vector-effect"],
    ["vertAdvY", "vert-adv-y"],
    ["vertOriginX", "vert-origin-x"],
    ["vertOriginY", "vert-origin-y"],
    ["wordSpacing", "word-spacing"],
    ["writingMode", "writing-mode"],
    ["xmlnsXlink", "xmlns:xlink"],
    ["xHeight", "x-height"]
  ]), Fm = /^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*:/i;
  function Qu(l) {
    return Fm.test("" + l) ? "javascript:throw new Error('React has blocked a javascript: URL as a security precaution.')" : l;
  }
  function Lt() {
  }
  var fi = null;
  function si(l) {
    return l = l.target || l.srcElement || window, l.correspondingUseElement && (l = l.correspondingUseElement), l.nodeType === 3 ? l.parentNode : l;
  }
  var ie = null, ce = null;
  function Vf(l) {
    var t = te(l);
    if (t && (l = t.stateNode)) {
      var a = l[lt] || null;
      l: switch (l = t.stateNode, t.type) {
        case "input":
          if (ni(
            l,
            a.value,
            a.defaultValue,
            a.defaultValue,
            a.checked,
            a.defaultChecked,
            a.type,
            a.name
          ), t = a.name, a.type === "radio" && t != null) {
            for (a = l; a.parentNode; ) a = a.parentNode;
            for (a = a.querySelectorAll(
              'input[name="' + zt(
                "" + t
              ) + '"][type="radio"]'
            ), t = 0; t < a.length; t++) {
              var e = a[t];
              if (e !== l && e.form === l.form) {
                var u = e[lt] || null;
                if (!u) throw Error(g(90));
                ni(
                  e,
                  u.value,
                  u.defaultValue,
                  u.defaultValue,
                  u.checked,
                  u.defaultChecked,
                  u.type,
                  u.name
                );
              }
            }
            for (t = 0; t < a.length; t++)
              e = a[t], e.form === l.form && Bf(e);
          }
          break l;
        case "textarea":
          Xf(l, a.value, a.defaultValue);
          break l;
        case "select":
          t = a.value, t != null && ue(l, !!a.multiple, t, !1);
      }
    }
  }
  var di = !1;
  function Kf(l, t, a) {
    if (di) return l(t, a);
    di = !0;
    try {
      var e = l(t);
      return e;
    } finally {
      if (di = !1, (ie !== null || ce !== null) && (On(), ie && (t = ie, l = ce, ce = ie = null, Vf(t), l)))
        for (t = 0; t < l.length; t++) Vf(l[t]);
    }
  }
  function Ve(l, t) {
    var a = l.stateNode;
    if (a === null) return null;
    var e = a[lt] || null;
    if (e === null) return null;
    a = e[t];
    l: switch (t) {
      case "onClick":
      case "onClickCapture":
      case "onDoubleClick":
      case "onDoubleClickCapture":
      case "onMouseDown":
      case "onMouseDownCapture":
      case "onMouseMove":
      case "onMouseMoveCapture":
      case "onMouseUp":
      case "onMouseUpCapture":
      case "onMouseEnter":
        (e = !e.disabled) || (l = l.type, e = !(l === "button" || l === "input" || l === "select" || l === "textarea")), l = !e;
        break l;
      default:
        l = !1;
    }
    if (l) return null;
    if (a && typeof a != "function")
      throw Error(
        g(231, t, typeof a)
      );
    return a;
  }
  var Vt = !(typeof window > "u" || typeof window.document > "u" || typeof window.document.createElement > "u"), oi = !1;
  if (Vt)
    try {
      var Ke = {};
      Object.defineProperty(Ke, "passive", {
        get: function() {
          oi = !0;
        }
      }), window.addEventListener("test", Ke, Ke), window.removeEventListener("test", Ke, Ke);
    } catch {
      oi = !1;
    }
  var fa = null, mi = null, Lu = null;
  function Jf() {
    if (Lu) return Lu;
    var l, t = mi, a = t.length, e, u = "value" in fa ? fa.value : fa.textContent, n = u.length;
    for (l = 0; l < a && t[l] === u[l]; l++) ;
    var i = a - l;
    for (e = 1; e <= i && t[a - e] === u[n - e]; e++) ;
    return Lu = u.slice(l, 1 < e ? 1 - e : void 0);
  }
  function Vu(l) {
    var t = l.keyCode;
    return "charCode" in l ? (l = l.charCode, l === 0 && t === 13 && (l = 13)) : l = t, l === 10 && (l = 13), 32 <= l || l === 13 ? l : 0;
  }
  function Ku() {
    return !0;
  }
  function wf() {
    return !1;
  }
  function tt(l) {
    function t(a, e, u, n, i) {
      this._reactName = a, this._targetInst = u, this.type = e, this.nativeEvent = n, this.target = i, this.currentTarget = null;
      for (var c in l)
        l.hasOwnProperty(c) && (a = l[c], this[c] = a ? a(n) : n[c]);
      return this.isDefaultPrevented = (n.defaultPrevented != null ? n.defaultPrevented : n.returnValue === !1) ? Ku : wf, this.isPropagationStopped = wf, this;
    }
    return C(t.prototype, {
      preventDefault: function() {
        this.defaultPrevented = !0;
        var a = this.nativeEvent;
        a && (a.preventDefault ? a.preventDefault() : typeof a.returnValue != "unknown" && (a.returnValue = !1), this.isDefaultPrevented = Ku);
      },
      stopPropagation: function() {
        var a = this.nativeEvent;
        a && (a.stopPropagation ? a.stopPropagation() : typeof a.cancelBubble != "unknown" && (a.cancelBubble = !0), this.isPropagationStopped = Ku);
      },
      persist: function() {
      },
      isPersistent: Ku
    }), t;
  }
  var qa = {
    eventPhase: 0,
    bubbles: 0,
    cancelable: 0,
    timeStamp: function(l) {
      return l.timeStamp || Date.now();
    },
    defaultPrevented: 0,
    isTrusted: 0
  }, Ju = tt(qa), Je = C({}, qa, { view: 0, detail: 0 }), Im = tt(Je), hi, vi, we, wu = C({}, Je, {
    screenX: 0,
    screenY: 0,
    clientX: 0,
    clientY: 0,
    pageX: 0,
    pageY: 0,
    ctrlKey: 0,
    shiftKey: 0,
    altKey: 0,
    metaKey: 0,
    getModifierState: ri,
    button: 0,
    buttons: 0,
    relatedTarget: function(l) {
      return l.relatedTarget === void 0 ? l.fromElement === l.srcElement ? l.toElement : l.fromElement : l.relatedTarget;
    },
    movementX: function(l) {
      return "movementX" in l ? l.movementX : (l !== we && (we && l.type === "mousemove" ? (hi = l.screenX - we.screenX, vi = l.screenY - we.screenY) : vi = hi = 0, we = l), hi);
    },
    movementY: function(l) {
      return "movementY" in l ? l.movementY : vi;
    }
  }), $f = tt(wu), Pm = C({}, wu, { dataTransfer: 0 }), lh = tt(Pm), th = C({}, Je, { relatedTarget: 0 }), yi = tt(th), ah = C({}, qa, {
    animationName: 0,
    elapsedTime: 0,
    pseudoElement: 0
  }), eh = tt(ah), uh = C({}, qa, {
    clipboardData: function(l) {
      return "clipboardData" in l ? l.clipboardData : window.clipboardData;
    }
  }), nh = tt(uh), ih = C({}, qa, { data: 0 }), Wf = tt(ih), ch = {
    Esc: "Escape",
    Spacebar: " ",
    Left: "ArrowLeft",
    Up: "ArrowUp",
    Right: "ArrowRight",
    Down: "ArrowDown",
    Del: "Delete",
    Win: "OS",
    Menu: "ContextMenu",
    Apps: "ContextMenu",
    Scroll: "ScrollLock",
    MozPrintableKey: "Unidentified"
  }, fh = {
    8: "Backspace",
    9: "Tab",
    12: "Clear",
    13: "Enter",
    16: "Shift",
    17: "Control",
    18: "Alt",
    19: "Pause",
    20: "CapsLock",
    27: "Escape",
    32: " ",
    33: "PageUp",
    34: "PageDown",
    35: "End",
    36: "Home",
    37: "ArrowLeft",
    38: "ArrowUp",
    39: "ArrowRight",
    40: "ArrowDown",
    45: "Insert",
    46: "Delete",
    112: "F1",
    113: "F2",
    114: "F3",
    115: "F4",
    116: "F5",
    117: "F6",
    118: "F7",
    119: "F8",
    120: "F9",
    121: "F10",
    122: "F11",
    123: "F12",
    144: "NumLock",
    145: "ScrollLock",
    224: "Meta"
  }, sh = {
    Alt: "altKey",
    Control: "ctrlKey",
    Meta: "metaKey",
    Shift: "shiftKey"
  };
  function dh(l) {
    var t = this.nativeEvent;
    return t.getModifierState ? t.getModifierState(l) : (l = sh[l]) ? !!t[l] : !1;
  }
  function ri() {
    return dh;
  }
  var oh = C({}, Je, {
    key: function(l) {
      if (l.key) {
        var t = ch[l.key] || l.key;
        if (t !== "Unidentified") return t;
      }
      return l.type === "keypress" ? (l = Vu(l), l === 13 ? "Enter" : String.fromCharCode(l)) : l.type === "keydown" || l.type === "keyup" ? fh[l.keyCode] || "Unidentified" : "";
    },
    code: 0,
    location: 0,
    ctrlKey: 0,
    shiftKey: 0,
    altKey: 0,
    metaKey: 0,
    repeat: 0,
    locale: 0,
    getModifierState: ri,
    charCode: function(l) {
      return l.type === "keypress" ? Vu(l) : 0;
    },
    keyCode: function(l) {
      return l.type === "keydown" || l.type === "keyup" ? l.keyCode : 0;
    },
    which: function(l) {
      return l.type === "keypress" ? Vu(l) : l.type === "keydown" || l.type === "keyup" ? l.keyCode : 0;
    }
  }), mh = tt(oh), hh = C({}, wu, {
    pointerId: 0,
    width: 0,
    height: 0,
    pressure: 0,
    tangentialPressure: 0,
    tiltX: 0,
    tiltY: 0,
    twist: 0,
    pointerType: 0,
    isPrimary: 0
  }), kf = tt(hh), vh = C({}, Je, {
    touches: 0,
    targetTouches: 0,
    changedTouches: 0,
    altKey: 0,
    metaKey: 0,
    ctrlKey: 0,
    shiftKey: 0,
    getModifierState: ri
  }), yh = tt(vh), rh = C({}, qa, {
    propertyName: 0,
    elapsedTime: 0,
    pseudoElement: 0
  }), gh = tt(rh), Sh = C({}, wu, {
    deltaX: function(l) {
      return "deltaX" in l ? l.deltaX : "wheelDeltaX" in l ? -l.wheelDeltaX : 0;
    },
    deltaY: function(l) {
      return "deltaY" in l ? l.deltaY : "wheelDeltaY" in l ? -l.wheelDeltaY : "wheelDelta" in l ? -l.wheelDelta : 0;
    },
    deltaZ: 0,
    deltaMode: 0
  }), bh = tt(Sh), ph = C({}, qa, {
    newState: 0,
    oldState: 0
  }), zh = tt(ph), Eh = [9, 13, 27, 32], gi = Vt && "CompositionEvent" in window, $e = null;
  Vt && "documentMode" in document && ($e = document.documentMode);
  var Ah = Vt && "TextEvent" in window && !$e, Ff = Vt && (!gi || $e && 8 < $e && 11 >= $e), If = " ", Pf = !1;
  function ls(l, t) {
    switch (l) {
      case "keyup":
        return Eh.indexOf(t.keyCode) !== -1;
      case "keydown":
        return t.keyCode !== 229;
      case "keypress":
      case "mousedown":
      case "focusout":
        return !0;
      default:
        return !1;
    }
  }
  function ts(l) {
    return l = l.detail, typeof l == "object" && "data" in l ? l.data : null;
  }
  var fe = !1;
  function Th(l, t) {
    switch (l) {
      case "compositionend":
        return ts(t);
      case "keypress":
        return t.which !== 32 ? null : (Pf = !0, If);
      case "textInput":
        return l = t.data, l === If && Pf ? null : l;
      default:
        return null;
    }
  }
  function xh(l, t) {
    if (fe)
      return l === "compositionend" || !gi && ls(l, t) ? (l = Jf(), Lu = mi = fa = null, fe = !1, l) : null;
    switch (l) {
      case "paste":
        return null;
      case "keypress":
        if (!(t.ctrlKey || t.altKey || t.metaKey) || t.ctrlKey && t.altKey) {
          if (t.char && 1 < t.char.length)
            return t.char;
          if (t.which) return String.fromCharCode(t.which);
        }
        return null;
      case "compositionend":
        return Ff && t.locale !== "ko" ? null : t.data;
      default:
        return null;
    }
  }
  var jh = {
    color: !0,
    date: !0,
    datetime: !0,
    "datetime-local": !0,
    email: !0,
    month: !0,
    number: !0,
    password: !0,
    range: !0,
    search: !0,
    tel: !0,
    text: !0,
    time: !0,
    url: !0,
    week: !0
  };
  function as(l) {
    var t = l && l.nodeName && l.nodeName.toLowerCase();
    return t === "input" ? !!jh[l.type] : t === "textarea";
  }
  function es(l, t, a, e) {
    ie ? ce ? ce.push(e) : ce = [e] : ie = e, t = Yn(t, "onChange"), 0 < t.length && (a = new Ju(
      "onChange",
      "change",
      null,
      a,
      e
    ), l.push({ event: a, listeners: t }));
  }
  var We = null, ke = null;
  function _h(l) {
    Xo(l, 0);
  }
  function $u(l) {
    var t = Le(l);
    if (Bf(t)) return l;
  }
  function us(l, t) {
    if (l === "change") return t;
  }
  var ns = !1;
  if (Vt) {
    var Si;
    if (Vt) {
      var bi = "oninput" in document;
      if (!bi) {
        var is = document.createElement("div");
        is.setAttribute("oninput", "return;"), bi = typeof is.oninput == "function";
      }
      Si = bi;
    } else Si = !1;
    ns = Si && (!document.documentMode || 9 < document.documentMode);
  }
  function cs() {
    We && (We.detachEvent("onpropertychange", fs), ke = We = null);
  }
  function fs(l) {
    if (l.propertyName === "value" && $u(ke)) {
      var t = [];
      es(
        t,
        ke,
        l,
        si(l)
      ), Kf(_h, t);
    }
  }
  function Nh(l, t, a) {
    l === "focusin" ? (cs(), We = t, ke = a, We.attachEvent("onpropertychange", fs)) : l === "focusout" && cs();
  }
  function Mh(l) {
    if (l === "selectionchange" || l === "keyup" || l === "keydown")
      return $u(ke);
  }
  function Oh(l, t) {
    if (l === "click") return $u(t);
  }
  function Dh(l, t) {
    if (l === "input" || l === "change")
      return $u(t);
  }
  function Uh(l, t) {
    return l === t && (l !== 0 || 1 / l === 1 / t) || l !== l && t !== t;
  }
  var st = typeof Object.is == "function" ? Object.is : Uh;
  function Fe(l, t) {
    if (st(l, t)) return !0;
    if (typeof l != "object" || l === null || typeof t != "object" || t === null)
      return !1;
    var a = Object.keys(l), e = Object.keys(t);
    if (a.length !== e.length) return !1;
    for (e = 0; e < a.length; e++) {
      var u = a[e];
      if (!R.call(t, u) || !st(l[u], t[u]))
        return !1;
    }
    return !0;
  }
  function ss(l) {
    for (; l && l.firstChild; ) l = l.firstChild;
    return l;
  }
  function ds(l, t) {
    var a = ss(l);
    l = 0;
    for (var e; a; ) {
      if (a.nodeType === 3) {
        if (e = l + a.textContent.length, l <= t && e >= t)
          return { node: a, offset: t - l };
        l = e;
      }
      l: {
        for (; a; ) {
          if (a.nextSibling) {
            a = a.nextSibling;
            break l;
          }
          a = a.parentNode;
        }
        a = void 0;
      }
      a = ss(a);
    }
  }
  function os(l, t) {
    return l && t ? l === t ? !0 : l && l.nodeType === 3 ? !1 : t && t.nodeType === 3 ? os(l, t.parentNode) : "contains" in l ? l.contains(t) : l.compareDocumentPosition ? !!(l.compareDocumentPosition(t) & 16) : !1 : !1;
  }
  function ms(l) {
    l = l != null && l.ownerDocument != null && l.ownerDocument.defaultView != null ? l.ownerDocument.defaultView : window;
    for (var t = Zu(l.document); t instanceof l.HTMLIFrameElement; ) {
      try {
        var a = typeof t.contentWindow.location.href == "string";
      } catch {
        a = !1;
      }
      if (a) l = t.contentWindow;
      else break;
      t = Zu(l.document);
    }
    return t;
  }
  function pi(l) {
    var t = l && l.nodeName && l.nodeName.toLowerCase();
    return t && (t === "input" && (l.type === "text" || l.type === "search" || l.type === "tel" || l.type === "url" || l.type === "password") || t === "textarea" || l.contentEditable === "true");
  }
  var Hh = Vt && "documentMode" in document && 11 >= document.documentMode, se = null, zi = null, Ie = null, Ei = !1;
  function hs(l, t, a) {
    var e = a.window === a ? a.document : a.nodeType === 9 ? a : a.ownerDocument;
    Ei || se == null || se !== Zu(e) || (e = se, "selectionStart" in e && pi(e) ? e = { start: e.selectionStart, end: e.selectionEnd } : (e = (e.ownerDocument && e.ownerDocument.defaultView || window).getSelection(), e = {
      anchorNode: e.anchorNode,
      anchorOffset: e.anchorOffset,
      focusNode: e.focusNode,
      focusOffset: e.focusOffset
    }), Ie && Fe(Ie, e) || (Ie = e, e = Yn(zi, "onSelect"), 0 < e.length && (t = new Ju(
      "onSelect",
      "select",
      null,
      t,
      a
    ), l.push({ event: t, listeners: e }), t.target = se)));
  }
  function Ya(l, t) {
    var a = {};
    return a[l.toLowerCase()] = t.toLowerCase(), a["Webkit" + l] = "webkit" + t, a["Moz" + l] = "moz" + t, a;
  }
  var de = {
    animationend: Ya("Animation", "AnimationEnd"),
    animationiteration: Ya("Animation", "AnimationIteration"),
    animationstart: Ya("Animation", "AnimationStart"),
    transitionrun: Ya("Transition", "TransitionRun"),
    transitionstart: Ya("Transition", "TransitionStart"),
    transitioncancel: Ya("Transition", "TransitionCancel"),
    transitionend: Ya("Transition", "TransitionEnd")
  }, Ai = {}, vs = {};
  Vt && (vs = document.createElement("div").style, "AnimationEvent" in window || (delete de.animationend.animation, delete de.animationiteration.animation, delete de.animationstart.animation), "TransitionEvent" in window || delete de.transitionend.transition);
  function Ba(l) {
    if (Ai[l]) return Ai[l];
    if (!de[l]) return l;
    var t = de[l], a;
    for (a in t)
      if (t.hasOwnProperty(a) && a in vs)
        return Ai[l] = t[a];
    return l;
  }
  var ys = Ba("animationend"), rs = Ba("animationiteration"), gs = Ba("animationstart"), Ch = Ba("transitionrun"), Rh = Ba("transitionstart"), qh = Ba("transitioncancel"), Ss = Ba("transitionend"), bs = /* @__PURE__ */ new Map(), Ti = "abort auxClick beforeToggle cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(
    " "
  );
  Ti.push("scrollEnd");
  function Dt(l, t) {
    bs.set(l, t), Ra(t, [l]);
  }
  var Wu = typeof reportError == "function" ? reportError : function(l) {
    if (typeof window == "object" && typeof window.ErrorEvent == "function") {
      var t = new window.ErrorEvent("error", {
        bubbles: !0,
        cancelable: !0,
        message: typeof l == "object" && l !== null && typeof l.message == "string" ? String(l.message) : String(l),
        error: l
      });
      if (!window.dispatchEvent(t)) return;
    } else if (typeof process == "object" && typeof process.emit == "function") {
      process.emit("uncaughtException", l);
      return;
    }
    console.error(l);
  }, Et = [], oe = 0, xi = 0;
  function ku() {
    for (var l = oe, t = xi = oe = 0; t < l; ) {
      var a = Et[t];
      Et[t++] = null;
      var e = Et[t];
      Et[t++] = null;
      var u = Et[t];
      Et[t++] = null;
      var n = Et[t];
      if (Et[t++] = null, e !== null && u !== null) {
        var i = e.pending;
        i === null ? u.next = u : (u.next = i.next, i.next = u), e.pending = u;
      }
      n !== 0 && ps(a, u, n);
    }
  }
  function Fu(l, t, a, e) {
    Et[oe++] = l, Et[oe++] = t, Et[oe++] = a, Et[oe++] = e, xi |= e, l.lanes |= e, l = l.alternate, l !== null && (l.lanes |= e);
  }
  function ji(l, t, a, e) {
    return Fu(l, t, a, e), Iu(l);
  }
  function Ga(l, t) {
    return Fu(l, null, null, t), Iu(l);
  }
  function ps(l, t, a) {
    l.lanes |= a;
    var e = l.alternate;
    e !== null && (e.lanes |= a);
    for (var u = !1, n = l.return; n !== null; )
      n.childLanes |= a, e = n.alternate, e !== null && (e.childLanes |= a), n.tag === 22 && (l = n.stateNode, l === null || l._visibility & 1 || (u = !0)), l = n, n = n.return;
    return l.tag === 3 ? (n = l.stateNode, u && t !== null && (u = 31 - ft(a), l = n.hiddenUpdates, e = l[u], e === null ? l[u] = [t] : e.push(t), t.lane = a | 536870912), n) : null;
  }
  function Iu(l) {
    if (50 < pu)
      throw pu = 0, Rc = null, Error(g(185));
    for (var t = l.return; t !== null; )
      l = t, t = l.return;
    return l.tag === 3 ? l.stateNode : null;
  }
  var me = {};
  function Yh(l, t, a, e) {
    this.tag = l, this.key = a, this.sibling = this.child = this.return = this.stateNode = this.type = this.elementType = null, this.index = 0, this.refCleanup = this.ref = null, this.pendingProps = t, this.dependencies = this.memoizedState = this.updateQueue = this.memoizedProps = null, this.mode = e, this.subtreeFlags = this.flags = 0, this.deletions = null, this.childLanes = this.lanes = 0, this.alternate = null;
  }
  function dt(l, t, a, e) {
    return new Yh(l, t, a, e);
  }
  function _i(l) {
    return l = l.prototype, !(!l || !l.isReactComponent);
  }
  function Kt(l, t) {
    var a = l.alternate;
    return a === null ? (a = dt(
      l.tag,
      t,
      l.key,
      l.mode
    ), a.elementType = l.elementType, a.type = l.type, a.stateNode = l.stateNode, a.alternate = l, l.alternate = a) : (a.pendingProps = t, a.type = l.type, a.flags = 0, a.subtreeFlags = 0, a.deletions = null), a.flags = l.flags & 65011712, a.childLanes = l.childLanes, a.lanes = l.lanes, a.child = l.child, a.memoizedProps = l.memoizedProps, a.memoizedState = l.memoizedState, a.updateQueue = l.updateQueue, t = l.dependencies, a.dependencies = t === null ? null : { lanes: t.lanes, firstContext: t.firstContext }, a.sibling = l.sibling, a.index = l.index, a.ref = l.ref, a.refCleanup = l.refCleanup, a;
  }
  function zs(l, t) {
    l.flags &= 65011714;
    var a = l.alternate;
    return a === null ? (l.childLanes = 0, l.lanes = t, l.child = null, l.subtreeFlags = 0, l.memoizedProps = null, l.memoizedState = null, l.updateQueue = null, l.dependencies = null, l.stateNode = null) : (l.childLanes = a.childLanes, l.lanes = a.lanes, l.child = a.child, l.subtreeFlags = 0, l.deletions = null, l.memoizedProps = a.memoizedProps, l.memoizedState = a.memoizedState, l.updateQueue = a.updateQueue, l.type = a.type, t = a.dependencies, l.dependencies = t === null ? null : {
      lanes: t.lanes,
      firstContext: t.firstContext
    }), l;
  }
  function Pu(l, t, a, e, u, n) {
    var i = 0;
    if (e = l, typeof l == "function") _i(l) && (i = 1);
    else if (typeof l == "string")
      i = Qv(
        l,
        a,
        O.current
      ) ? 26 : l === "html" || l === "head" || l === "body" ? 27 : 5;
    else
      l: switch (l) {
        case kl:
          return l = dt(31, a, t, u), l.elementType = kl, l.lanes = n, l;
        case Cl:
          return Xa(a.children, u, n, t);
        case rt:
          i = 8, u |= 24;
          break;
        case Yl:
          return l = dt(12, a, t, u | 2), l.elementType = Yl, l.lanes = n, l;
        case Sl:
          return l = dt(13, a, t, u), l.elementType = Sl, l.lanes = n, l;
        case bl:
          return l = dt(19, a, t, u), l.elementType = bl, l.lanes = n, l;
        default:
          if (typeof l == "object" && l !== null)
            switch (l.$$typeof) {
              case Bl:
                i = 10;
                break l;
              case Ct:
                i = 9;
                break l;
              case Ql:
                i = 11;
                break l;
              case w:
                i = 14;
                break l;
              case Ll:
                i = 16, e = null;
                break l;
            }
          i = 29, a = Error(
            g(130, l === null ? "null" : typeof l, "")
          ), e = null;
      }
    return t = dt(i, a, t, u), t.elementType = l, t.type = e, t.lanes = n, t;
  }
  function Xa(l, t, a, e) {
    return l = dt(7, l, e, t), l.lanes = a, l;
  }
  function Ni(l, t, a) {
    return l = dt(6, l, null, t), l.lanes = a, l;
  }
  function Es(l) {
    var t = dt(18, null, null, 0);
    return t.stateNode = l, t;
  }
  function Mi(l, t, a) {
    return t = dt(
      4,
      l.children !== null ? l.children : [],
      l.key,
      t
    ), t.lanes = a, t.stateNode = {
      containerInfo: l.containerInfo,
      pendingChildren: null,
      implementation: l.implementation
    }, t;
  }
  var As = /* @__PURE__ */ new WeakMap();
  function At(l, t) {
    if (typeof l == "object" && l !== null) {
      var a = As.get(l);
      return a !== void 0 ? a : (t = {
        value: l,
        source: t,
        stack: N(t)
      }, As.set(l, t), t);
    }
    return {
      value: l,
      source: t,
      stack: N(t)
    };
  }
  var he = [], ve = 0, ln = null, Pe = 0, Tt = [], xt = 0, sa = null, Yt = 1, Bt = "";
  function Jt(l, t) {
    he[ve++] = Pe, he[ve++] = ln, ln = l, Pe = t;
  }
  function Ts(l, t, a) {
    Tt[xt++] = Yt, Tt[xt++] = Bt, Tt[xt++] = sa, sa = l;
    var e = Yt;
    l = Bt;
    var u = 32 - ft(e) - 1;
    e &= ~(1 << u), a += 1;
    var n = 32 - ft(t) + u;
    if (30 < n) {
      var i = u - u % 5;
      n = (e & (1 << i) - 1).toString(32), e >>= i, u -= i, Yt = 1 << 32 - ft(t) + u | a << u | e, Bt = n + l;
    } else
      Yt = 1 << n | a << u | e, Bt = l;
  }
  function Oi(l) {
    l.return !== null && (Jt(l, 1), Ts(l, 1, 0));
  }
  function Di(l) {
    for (; l === ln; )
      ln = he[--ve], he[ve] = null, Pe = he[--ve], he[ve] = null;
    for (; l === sa; )
      sa = Tt[--xt], Tt[xt] = null, Bt = Tt[--xt], Tt[xt] = null, Yt = Tt[--xt], Tt[xt] = null;
  }
  function xs(l, t) {
    Tt[xt++] = Yt, Tt[xt++] = Bt, Tt[xt++] = sa, Yt = t.id, Bt = t.overflow, sa = l;
  }
  var Kl = null, rl = null, I = !1, da = null, jt = !1, Ui = Error(g(519));
  function oa(l) {
    var t = Error(
      g(
        418,
        1 < arguments.length && arguments[1] !== void 0 && arguments[1] ? "text" : "HTML",
        ""
      )
    );
    throw lu(At(t, l)), Ui;
  }
  function js(l) {
    var t = l.stateNode, a = l.type, e = l.memoizedProps;
    switch (t[Vl] = l, t[lt] = e, a) {
      case "dialog":
        W("cancel", t), W("close", t);
        break;
      case "iframe":
      case "object":
      case "embed":
        W("load", t);
        break;
      case "video":
      case "audio":
        for (a = 0; a < Eu.length; a++)
          W(Eu[a], t);
        break;
      case "source":
        W("error", t);
        break;
      case "img":
      case "image":
      case "link":
        W("error", t), W("load", t);
        break;
      case "details":
        W("toggle", t);
        break;
      case "input":
        W("invalid", t), Gf(
          t,
          e.value,
          e.defaultValue,
          e.checked,
          e.defaultChecked,
          e.type,
          e.name,
          !0
        );
        break;
      case "select":
        W("invalid", t);
        break;
      case "textarea":
        W("invalid", t), Zf(t, e.value, e.defaultValue, e.children);
    }
    a = e.children, typeof a != "string" && typeof a != "number" && typeof a != "bigint" || t.textContent === "" + a || e.suppressHydrationWarning === !0 || Vo(t.textContent, a) ? (e.popover != null && (W("beforetoggle", t), W("toggle", t)), e.onScroll != null && W("scroll", t), e.onScrollEnd != null && W("scrollend", t), e.onClick != null && (t.onclick = Lt), t = !0) : t = !1, t || oa(l, !0);
  }
  function _s(l) {
    for (Kl = l.return; Kl; )
      switch (Kl.tag) {
        case 5:
        case 31:
        case 13:
          jt = !1;
          return;
        case 27:
        case 3:
          jt = !0;
          return;
        default:
          Kl = Kl.return;
      }
  }
  function ye(l) {
    if (l !== Kl) return !1;
    if (!I) return _s(l), I = !0, !1;
    var t = l.tag, a;
    if ((a = t !== 3 && t !== 27) && ((a = t === 5) && (a = l.type, a = !(a !== "form" && a !== "button") || kc(l.type, l.memoizedProps)), a = !a), a && rl && oa(l), _s(l), t === 13) {
      if (l = l.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(317));
      rl = Po(l);
    } else if (t === 31) {
      if (l = l.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(317));
      rl = Po(l);
    } else
      t === 27 ? (t = rl, xa(l.type) ? (l = tf, tf = null, rl = l) : rl = t) : rl = Kl ? Nt(l.stateNode.nextSibling) : null;
    return !0;
  }
  function Za() {
    rl = Kl = null, I = !1;
  }
  function Hi() {
    var l = da;
    return l !== null && (nt === null ? nt = l : nt.push.apply(
      nt,
      l
    ), da = null), l;
  }
  function lu(l) {
    da === null ? da = [l] : da.push(l);
  }
  var Ci = o(null), Qa = null, wt = null;
  function ma(l, t, a) {
    j(Ci, t._currentValue), t._currentValue = a;
  }
  function $t(l) {
    l._currentValue = Ci.current, E(Ci);
  }
  function Ri(l, t, a) {
    for (; l !== null; ) {
      var e = l.alternate;
      if ((l.childLanes & t) !== t ? (l.childLanes |= t, e !== null && (e.childLanes |= t)) : e !== null && (e.childLanes & t) !== t && (e.childLanes |= t), l === a) break;
      l = l.return;
    }
  }
  function qi(l, t, a, e) {
    var u = l.child;
    for (u !== null && (u.return = l); u !== null; ) {
      var n = u.dependencies;
      if (n !== null) {
        var i = u.child;
        n = n.firstContext;
        l: for (; n !== null; ) {
          var c = n;
          n = u;
          for (var s = 0; s < t.length; s++)
            if (c.context === t[s]) {
              n.lanes |= a, c = n.alternate, c !== null && (c.lanes |= a), Ri(
                n.return,
                a,
                l
              ), e || (i = null);
              break l;
            }
          n = c.next;
        }
      } else if (u.tag === 18) {
        if (i = u.return, i === null) throw Error(g(341));
        i.lanes |= a, n = i.alternate, n !== null && (n.lanes |= a), Ri(i, a, l), i = null;
      } else i = u.child;
      if (i !== null) i.return = u;
      else
        for (i = u; i !== null; ) {
          if (i === l) {
            i = null;
            break;
          }
          if (u = i.sibling, u !== null) {
            u.return = i.return, i = u;
            break;
          }
          i = i.return;
        }
      u = i;
    }
  }
  function re(l, t, a, e) {
    l = null;
    for (var u = t, n = !1; u !== null; ) {
      if (!n) {
        if ((u.flags & 524288) !== 0) n = !0;
        else if ((u.flags & 262144) !== 0) break;
      }
      if (u.tag === 10) {
        var i = u.alternate;
        if (i === null) throw Error(g(387));
        if (i = i.memoizedProps, i !== null) {
          var c = u.type;
          st(u.pendingProps.value, i.value) || (l !== null ? l.push(c) : l = [c]);
        }
      } else if (u === al.current) {
        if (i = u.alternate, i === null) throw Error(g(387));
        i.memoizedState.memoizedState !== u.memoizedState.memoizedState && (l !== null ? l.push(_u) : l = [_u]);
      }
      u = u.return;
    }
    l !== null && qi(
      t,
      l,
      a,
      e
    ), t.flags |= 262144;
  }
  function tn(l) {
    for (l = l.firstContext; l !== null; ) {
      if (!st(
        l.context._currentValue,
        l.memoizedValue
      ))
        return !0;
      l = l.next;
    }
    return !1;
  }
  function La(l) {
    Qa = l, wt = null, l = l.dependencies, l !== null && (l.firstContext = null);
  }
  function Jl(l) {
    return Ns(Qa, l);
  }
  function an(l, t) {
    return Qa === null && La(l), Ns(l, t);
  }
  function Ns(l, t) {
    var a = t._currentValue;
    if (t = { context: t, memoizedValue: a, next: null }, wt === null) {
      if (l === null) throw Error(g(308));
      wt = t, l.dependencies = { lanes: 0, firstContext: t }, l.flags |= 524288;
    } else wt = wt.next = t;
    return a;
  }
  var Bh = typeof AbortController < "u" ? AbortController : function() {
    var l = [], t = this.signal = {
      aborted: !1,
      addEventListener: function(a, e) {
        l.push(e);
      }
    };
    this.abort = function() {
      t.aborted = !0, l.forEach(function(a) {
        return a();
      });
    };
  }, Gh = M.unstable_scheduleCallback, Xh = M.unstable_NormalPriority, Ml = {
    $$typeof: Bl,
    Consumer: null,
    Provider: null,
    _currentValue: null,
    _currentValue2: null,
    _threadCount: 0
  };
  function Yi() {
    return {
      controller: new Bh(),
      data: /* @__PURE__ */ new Map(),
      refCount: 0
    };
  }
  function tu(l) {
    l.refCount--, l.refCount === 0 && Gh(Xh, function() {
      l.controller.abort();
    });
  }
  var au = null, Bi = 0, ge = 0, Se = null;
  function Zh(l, t) {
    if (au === null) {
      var a = au = [];
      Bi = 0, ge = Zc(), Se = {
        status: "pending",
        value: void 0,
        then: function(e) {
          a.push(e);
        }
      };
    }
    return Bi++, t.then(Ms, Ms), t;
  }
  function Ms() {
    if (--Bi === 0 && au !== null) {
      Se !== null && (Se.status = "fulfilled");
      var l = au;
      au = null, ge = 0, Se = null;
      for (var t = 0; t < l.length; t++) (0, l[t])();
    }
  }
  function Qh(l, t) {
    var a = [], e = {
      status: "pending",
      value: null,
      reason: null,
      then: function(u) {
        a.push(u);
      }
    };
    return l.then(
      function() {
        e.status = "fulfilled", e.value = t;
        for (var u = 0; u < a.length; u++) (0, a[u])(t);
      },
      function(u) {
        for (e.status = "rejected", e.reason = u, u = 0; u < a.length; u++)
          (0, a[u])(void 0);
      }
    ), e;
  }
  var Os = b.S;
  b.S = function(l, t) {
    vo = Fl(), typeof t == "object" && t !== null && typeof t.then == "function" && Zh(l, t), Os !== null && Os(l, t);
  };
  var Va = o(null);
  function Gi() {
    var l = Va.current;
    return l !== null ? l : vl.pooledCache;
  }
  function en(l, t) {
    t === null ? j(Va, Va.current) : j(Va, t.pool);
  }
  function Ds() {
    var l = Gi();
    return l === null ? null : { parent: Ml._currentValue, pool: l };
  }
  var be = Error(g(460)), Xi = Error(g(474)), un = Error(g(542)), nn = { then: function() {
  } };
  function Us(l) {
    return l = l.status, l === "fulfilled" || l === "rejected";
  }
  function Hs(l, t, a) {
    switch (a = l[a], a === void 0 ? l.push(t) : a !== t && (t.then(Lt, Lt), t = a), t.status) {
      case "fulfilled":
        return t.value;
      case "rejected":
        throw l = t.reason, Rs(l), l;
      default:
        if (typeof t.status == "string") t.then(Lt, Lt);
        else {
          if (l = vl, l !== null && 100 < l.shellSuspendCounter)
            throw Error(g(482));
          l = t, l.status = "pending", l.then(
            function(e) {
              if (t.status === "pending") {
                var u = t;
                u.status = "fulfilled", u.value = e;
              }
            },
            function(e) {
              if (t.status === "pending") {
                var u = t;
                u.status = "rejected", u.reason = e;
              }
            }
          );
        }
        switch (t.status) {
          case "fulfilled":
            return t.value;
          case "rejected":
            throw l = t.reason, Rs(l), l;
        }
        throw Ja = t, be;
    }
  }
  function Ka(l) {
    try {
      var t = l._init;
      return t(l._payload);
    } catch (a) {
      throw a !== null && typeof a == "object" && typeof a.then == "function" ? (Ja = a, be) : a;
    }
  }
  var Ja = null;
  function Cs() {
    if (Ja === null) throw Error(g(459));
    var l = Ja;
    return Ja = null, l;
  }
  function Rs(l) {
    if (l === be || l === un)
      throw Error(g(483));
  }
  var pe = null, eu = 0;
  function cn(l) {
    var t = eu;
    return eu += 1, pe === null && (pe = []), Hs(pe, l, t);
  }
  function uu(l, t) {
    t = t.props.ref, l.ref = t !== void 0 ? t : null;
  }
  function fn(l, t) {
    throw t.$$typeof === yl ? Error(g(525)) : (l = Object.prototype.toString.call(t), Error(
      g(
        31,
        l === "[object Object]" ? "object with keys {" + Object.keys(t).join(", ") + "}" : l
      )
    ));
  }
  function qs(l) {
    function t(h, m) {
      if (l) {
        var v = h.deletions;
        v === null ? (h.deletions = [m], h.flags |= 16) : v.push(m);
      }
    }
    function a(h, m) {
      if (!l) return null;
      for (; m !== null; )
        t(h, m), m = m.sibling;
      return null;
    }
    function e(h) {
      for (var m = /* @__PURE__ */ new Map(); h !== null; )
        h.key !== null ? m.set(h.key, h) : m.set(h.index, h), h = h.sibling;
      return m;
    }
    function u(h, m) {
      return h = Kt(h, m), h.index = 0, h.sibling = null, h;
    }
    function n(h, m, v) {
      return h.index = v, l ? (v = h.alternate, v !== null ? (v = v.index, v < m ? (h.flags |= 67108866, m) : v) : (h.flags |= 67108866, m)) : (h.flags |= 1048576, m);
    }
    function i(h) {
      return l && h.alternate === null && (h.flags |= 67108866), h;
    }
    function c(h, m, v, A) {
      return m === null || m.tag !== 6 ? (m = Ni(v, h.mode, A), m.return = h, m) : (m = u(m, v), m.return = h, m);
    }
    function s(h, m, v, A) {
      var q = v.type;
      return q === Cl ? z(
        h,
        m,
        v.props.children,
        A,
        v.key
      ) : m !== null && (m.elementType === q || typeof q == "object" && q !== null && q.$$typeof === Ll && Ka(q) === m.type) ? (m = u(m, v.props), uu(m, v), m.return = h, m) : (m = Pu(
        v.type,
        v.key,
        v.props,
        null,
        h.mode,
        A
      ), uu(m, v), m.return = h, m);
    }
    function y(h, m, v, A) {
      return m === null || m.tag !== 4 || m.stateNode.containerInfo !== v.containerInfo || m.stateNode.implementation !== v.implementation ? (m = Mi(v, h.mode, A), m.return = h, m) : (m = u(m, v.children || []), m.return = h, m);
    }
    function z(h, m, v, A, q) {
      return m === null || m.tag !== 7 ? (m = Xa(
        v,
        h.mode,
        A,
        q
      ), m.return = h, m) : (m = u(m, v), m.return = h, m);
    }
    function T(h, m, v) {
      if (typeof m == "string" && m !== "" || typeof m == "number" || typeof m == "bigint")
        return m = Ni(
          "" + m,
          h.mode,
          v
        ), m.return = h, m;
      if (typeof m == "object" && m !== null) {
        switch (m.$$typeof) {
          case jl:
            return v = Pu(
              m.type,
              m.key,
              m.props,
              null,
              h.mode,
              v
            ), uu(v, m), v.return = h, v;
          case Zl:
            return m = Mi(
              m,
              h.mode,
              v
            ), m.return = h, m;
          case Ll:
            return m = Ka(m), T(h, m, v);
        }
        if (Pl(m) || zl(m))
          return m = Xa(
            m,
            h.mode,
            v,
            null
          ), m.return = h, m;
        if (typeof m.then == "function")
          return T(h, cn(m), v);
        if (m.$$typeof === Bl)
          return T(
            h,
            an(h, m),
            v
          );
        fn(h, m);
      }
      return null;
    }
    function r(h, m, v, A) {
      var q = m !== null ? m.key : null;
      if (typeof v == "string" && v !== "" || typeof v == "number" || typeof v == "bigint")
        return q !== null ? null : c(h, m, "" + v, A);
      if (typeof v == "object" && v !== null) {
        switch (v.$$typeof) {
          case jl:
            return v.key === q ? s(h, m, v, A) : null;
          case Zl:
            return v.key === q ? y(h, m, v, A) : null;
          case Ll:
            return v = Ka(v), r(h, m, v, A);
        }
        if (Pl(v) || zl(v))
          return q !== null ? null : z(h, m, v, A, null);
        if (typeof v.then == "function")
          return r(
            h,
            m,
            cn(v),
            A
          );
        if (v.$$typeof === Bl)
          return r(
            h,
            m,
            an(h, v),
            A
          );
        fn(h, v);
      }
      return null;
    }
    function S(h, m, v, A, q) {
      if (typeof A == "string" && A !== "" || typeof A == "number" || typeof A == "bigint")
        return h = h.get(v) || null, c(m, h, "" + A, q);
      if (typeof A == "object" && A !== null) {
        switch (A.$$typeof) {
          case jl:
            return h = h.get(
              A.key === null ? v : A.key
            ) || null, s(m, h, A, q);
          case Zl:
            return h = h.get(
              A.key === null ? v : A.key
            ) || null, y(m, h, A, q);
          case Ll:
            return A = Ka(A), S(
              h,
              m,
              v,
              A,
              q
            );
        }
        if (Pl(A) || zl(A))
          return h = h.get(v) || null, z(m, h, A, q, null);
        if (typeof A.then == "function")
          return S(
            h,
            m,
            v,
            cn(A),
            q
          );
        if (A.$$typeof === Bl)
          return S(
            h,
            m,
            v,
            an(m, A),
            q
          );
        fn(m, A);
      }
      return null;
    }
    function D(h, m, v, A) {
      for (var q = null, el = null, H = m, V = m = 0, F = null; H !== null && V < v.length; V++) {
        H.index > V ? (F = H, H = null) : F = H.sibling;
        var ul = r(
          h,
          H,
          v[V],
          A
        );
        if (ul === null) {
          H === null && (H = F);
          break;
        }
        l && H && ul.alternate === null && t(h, H), m = n(ul, m, V), el === null ? q = ul : el.sibling = ul, el = ul, H = F;
      }
      if (V === v.length)
        return a(h, H), I && Jt(h, V), q;
      if (H === null) {
        for (; V < v.length; V++)
          H = T(h, v[V], A), H !== null && (m = n(
            H,
            m,
            V
          ), el === null ? q = H : el.sibling = H, el = H);
        return I && Jt(h, V), q;
      }
      for (H = e(H); V < v.length; V++)
        F = S(
          H,
          h,
          V,
          v[V],
          A
        ), F !== null && (l && F.alternate !== null && H.delete(
          F.key === null ? V : F.key
        ), m = n(
          F,
          m,
          V
        ), el === null ? q = F : el.sibling = F, el = F);
      return l && H.forEach(function(Oa) {
        return t(h, Oa);
      }), I && Jt(h, V), q;
    }
    function B(h, m, v, A) {
      if (v == null) throw Error(g(151));
      for (var q = null, el = null, H = m, V = m = 0, F = null, ul = v.next(); H !== null && !ul.done; V++, ul = v.next()) {
        H.index > V ? (F = H, H = null) : F = H.sibling;
        var Oa = r(h, H, ul.value, A);
        if (Oa === null) {
          H === null && (H = F);
          break;
        }
        l && H && Oa.alternate === null && t(h, H), m = n(Oa, m, V), el === null ? q = Oa : el.sibling = Oa, el = Oa, H = F;
      }
      if (ul.done)
        return a(h, H), I && Jt(h, V), q;
      if (H === null) {
        for (; !ul.done; V++, ul = v.next())
          ul = T(h, ul.value, A), ul !== null && (m = n(ul, m, V), el === null ? q = ul : el.sibling = ul, el = ul);
        return I && Jt(h, V), q;
      }
      for (H = e(H); !ul.done; V++, ul = v.next())
        ul = S(H, h, V, ul.value, A), ul !== null && (l && ul.alternate !== null && H.delete(ul.key === null ? V : ul.key), m = n(ul, m, V), el === null ? q = ul : el.sibling = ul, el = ul);
      return l && H.forEach(function(Pv) {
        return t(h, Pv);
      }), I && Jt(h, V), q;
    }
    function hl(h, m, v, A) {
      if (typeof v == "object" && v !== null && v.type === Cl && v.key === null && (v = v.props.children), typeof v == "object" && v !== null) {
        switch (v.$$typeof) {
          case jl:
            l: {
              for (var q = v.key; m !== null; ) {
                if (m.key === q) {
                  if (q = v.type, q === Cl) {
                    if (m.tag === 7) {
                      a(
                        h,
                        m.sibling
                      ), A = u(
                        m,
                        v.props.children
                      ), A.return = h, h = A;
                      break l;
                    }
                  } else if (m.elementType === q || typeof q == "object" && q !== null && q.$$typeof === Ll && Ka(q) === m.type) {
                    a(
                      h,
                      m.sibling
                    ), A = u(m, v.props), uu(A, v), A.return = h, h = A;
                    break l;
                  }
                  a(h, m);
                  break;
                } else t(h, m);
                m = m.sibling;
              }
              v.type === Cl ? (A = Xa(
                v.props.children,
                h.mode,
                A,
                v.key
              ), A.return = h, h = A) : (A = Pu(
                v.type,
                v.key,
                v.props,
                null,
                h.mode,
                A
              ), uu(A, v), A.return = h, h = A);
            }
            return i(h);
          case Zl:
            l: {
              for (q = v.key; m !== null; ) {
                if (m.key === q)
                  if (m.tag === 4 && m.stateNode.containerInfo === v.containerInfo && m.stateNode.implementation === v.implementation) {
                    a(
                      h,
                      m.sibling
                    ), A = u(m, v.children || []), A.return = h, h = A;
                    break l;
                  } else {
                    a(h, m);
                    break;
                  }
                else t(h, m);
                m = m.sibling;
              }
              A = Mi(v, h.mode, A), A.return = h, h = A;
            }
            return i(h);
          case Ll:
            return v = Ka(v), hl(
              h,
              m,
              v,
              A
            );
        }
        if (Pl(v))
          return D(
            h,
            m,
            v,
            A
          );
        if (zl(v)) {
          if (q = zl(v), typeof q != "function") throw Error(g(150));
          return v = q.call(v), B(
            h,
            m,
            v,
            A
          );
        }
        if (typeof v.then == "function")
          return hl(
            h,
            m,
            cn(v),
            A
          );
        if (v.$$typeof === Bl)
          return hl(
            h,
            m,
            an(h, v),
            A
          );
        fn(h, v);
      }
      return typeof v == "string" && v !== "" || typeof v == "number" || typeof v == "bigint" ? (v = "" + v, m !== null && m.tag === 6 ? (a(h, m.sibling), A = u(m, v), A.return = h, h = A) : (a(h, m), A = Ni(v, h.mode, A), A.return = h, h = A), i(h)) : a(h, m);
    }
    return function(h, m, v, A) {
      try {
        eu = 0;
        var q = hl(
          h,
          m,
          v,
          A
        );
        return pe = null, q;
      } catch (H) {
        if (H === be || H === un) throw H;
        var el = dt(29, H, null, h.mode);
        return el.lanes = A, el.return = h, el;
      }
    };
  }
  var wa = qs(!0), Ys = qs(!1), ha = !1;
  function Zi(l) {
    l.updateQueue = {
      baseState: l.memoizedState,
      firstBaseUpdate: null,
      lastBaseUpdate: null,
      shared: { pending: null, lanes: 0, hiddenCallbacks: null },
      callbacks: null
    };
  }
  function Qi(l, t) {
    l = l.updateQueue, t.updateQueue === l && (t.updateQueue = {
      baseState: l.baseState,
      firstBaseUpdate: l.firstBaseUpdate,
      lastBaseUpdate: l.lastBaseUpdate,
      shared: l.shared,
      callbacks: null
    });
  }
  function va(l) {
    return { lane: l, tag: 0, payload: null, callback: null, next: null };
  }
  function ya(l, t, a) {
    var e = l.updateQueue;
    if (e === null) return null;
    if (e = e.shared, (il & 2) !== 0) {
      var u = e.pending;
      return u === null ? t.next = t : (t.next = u.next, u.next = t), e.pending = t, t = Iu(l), ps(l, null, a), t;
    }
    return Fu(l, e, t, a), Iu(l);
  }
  function nu(l, t, a) {
    if (t = t.updateQueue, t !== null && (t = t.shared, (a & 4194048) !== 0)) {
      var e = t.lanes;
      e &= l.pendingLanes, a |= e, t.lanes = a, Nf(l, a);
    }
  }
  function Li(l, t) {
    var a = l.updateQueue, e = l.alternate;
    if (e !== null && (e = e.updateQueue, a === e)) {
      var u = null, n = null;
      if (a = a.firstBaseUpdate, a !== null) {
        do {
          var i = {
            lane: a.lane,
            tag: a.tag,
            payload: a.payload,
            callback: null,
            next: null
          };
          n === null ? u = n = i : n = n.next = i, a = a.next;
        } while (a !== null);
        n === null ? u = n = t : n = n.next = t;
      } else u = n = t;
      a = {
        baseState: e.baseState,
        firstBaseUpdate: u,
        lastBaseUpdate: n,
        shared: e.shared,
        callbacks: e.callbacks
      }, l.updateQueue = a;
      return;
    }
    l = a.lastBaseUpdate, l === null ? a.firstBaseUpdate = t : l.next = t, a.lastBaseUpdate = t;
  }
  var Vi = !1;
  function iu() {
    if (Vi) {
      var l = Se;
      if (l !== null) throw l;
    }
  }
  function cu(l, t, a, e) {
    Vi = !1;
    var u = l.updateQueue;
    ha = !1;
    var n = u.firstBaseUpdate, i = u.lastBaseUpdate, c = u.shared.pending;
    if (c !== null) {
      u.shared.pending = null;
      var s = c, y = s.next;
      s.next = null, i === null ? n = y : i.next = y, i = s;
      var z = l.alternate;
      z !== null && (z = z.updateQueue, c = z.lastBaseUpdate, c !== i && (c === null ? z.firstBaseUpdate = y : c.next = y, z.lastBaseUpdate = s));
    }
    if (n !== null) {
      var T = u.baseState;
      i = 0, z = y = s = null, c = n;
      do {
        var r = c.lane & -536870913, S = r !== c.lane;
        if (S ? (k & r) === r : (e & r) === r) {
          r !== 0 && r === ge && (Vi = !0), z !== null && (z = z.next = {
            lane: 0,
            tag: c.tag,
            payload: c.payload,
            callback: null,
            next: null
          });
          l: {
            var D = l, B = c;
            r = t;
            var hl = a;
            switch (B.tag) {
              case 1:
                if (D = B.payload, typeof D == "function") {
                  T = D.call(hl, T, r);
                  break l;
                }
                T = D;
                break l;
              case 3:
                D.flags = D.flags & -65537 | 128;
              case 0:
                if (D = B.payload, r = typeof D == "function" ? D.call(hl, T, r) : D, r == null) break l;
                T = C({}, T, r);
                break l;
              case 2:
                ha = !0;
            }
          }
          r = c.callback, r !== null && (l.flags |= 64, S && (l.flags |= 8192), S = u.callbacks, S === null ? u.callbacks = [r] : S.push(r));
        } else
          S = {
            lane: r,
            tag: c.tag,
            payload: c.payload,
            callback: c.callback,
            next: null
          }, z === null ? (y = z = S, s = T) : z = z.next = S, i |= r;
        if (c = c.next, c === null) {
          if (c = u.shared.pending, c === null)
            break;
          S = c, c = S.next, S.next = null, u.lastBaseUpdate = S, u.shared.pending = null;
        }
      } while (!0);
      z === null && (s = T), u.baseState = s, u.firstBaseUpdate = y, u.lastBaseUpdate = z, n === null && (u.shared.lanes = 0), pa |= i, l.lanes = i, l.memoizedState = T;
    }
  }
  function Bs(l, t) {
    if (typeof l != "function")
      throw Error(g(191, l));
    l.call(t);
  }
  function Gs(l, t) {
    var a = l.callbacks;
    if (a !== null)
      for (l.callbacks = null, l = 0; l < a.length; l++)
        Bs(a[l], t);
  }
  var ze = o(null), sn = o(0);
  function Xs(l, t) {
    l = ea, j(sn, l), j(ze, t), ea = l | t.baseLanes;
  }
  function Ki() {
    j(sn, ea), j(ze, ze.current);
  }
  function Ji() {
    ea = sn.current, E(ze), E(sn);
  }
  var ot = o(null), _t = null;
  function ra(l) {
    var t = l.alternate;
    j(_l, _l.current & 1), j(ot, l), _t === null && (t === null || ze.current !== null || t.memoizedState !== null) && (_t = l);
  }
  function wi(l) {
    j(_l, _l.current), j(ot, l), _t === null && (_t = l);
  }
  function Zs(l) {
    l.tag === 22 ? (j(_l, _l.current), j(ot, l), _t === null && (_t = l)) : ga();
  }
  function ga() {
    j(_l, _l.current), j(ot, ot.current);
  }
  function mt(l) {
    E(ot), _t === l && (_t = null), E(_l);
  }
  var _l = o(0);
  function dn(l) {
    for (var t = l; t !== null; ) {
      if (t.tag === 13) {
        var a = t.memoizedState;
        if (a !== null && (a = a.dehydrated, a === null || Pc(a) || lf(a)))
          return t;
      } else if (t.tag === 19 && (t.memoizedProps.revealOrder === "forwards" || t.memoizedProps.revealOrder === "backwards" || t.memoizedProps.revealOrder === "unstable_legacy-backwards" || t.memoizedProps.revealOrder === "together")) {
        if ((t.flags & 128) !== 0) return t;
      } else if (t.child !== null) {
        t.child.return = t, t = t.child;
        continue;
      }
      if (t === l) break;
      for (; t.sibling === null; ) {
        if (t.return === null || t.return === l) return null;
        t = t.return;
      }
      t.sibling.return = t.return, t = t.sibling;
    }
    return null;
  }
  var Wt = 0, L = null, ol = null, Ol = null, on = !1, Ee = !1, $a = !1, mn = 0, fu = 0, Ae = null, Lh = 0;
  function Tl() {
    throw Error(g(321));
  }
  function $i(l, t) {
    if (t === null) return !1;
    for (var a = 0; a < t.length && a < l.length; a++)
      if (!st(l[a], t[a])) return !1;
    return !0;
  }
  function Wi(l, t, a, e, u, n) {
    return Wt = n, L = t, t.memoizedState = null, t.updateQueue = null, t.lanes = 0, b.H = l === null || l.memoizedState === null ? Td : dc, $a = !1, n = a(e, u), $a = !1, Ee && (n = Ls(
      t,
      a,
      e,
      u
    )), Qs(l), n;
  }
  function Qs(l) {
    b.H = ou;
    var t = ol !== null && ol.next !== null;
    if (Wt = 0, Ol = ol = L = null, on = !1, fu = 0, Ae = null, t) throw Error(g(300));
    l === null || Dl || (l = l.dependencies, l !== null && tn(l) && (Dl = !0));
  }
  function Ls(l, t, a, e) {
    L = l;
    var u = 0;
    do {
      if (Ee && (Ae = null), fu = 0, Ee = !1, 25 <= u) throw Error(g(301));
      if (u += 1, Ol = ol = null, l.updateQueue != null) {
        var n = l.updateQueue;
        n.lastEffect = null, n.events = null, n.stores = null, n.memoCache != null && (n.memoCache.index = 0);
      }
      b.H = xd, n = t(a, e);
    } while (Ee);
    return n;
  }
  function Vh() {
    var l = b.H, t = l.useState()[0];
    return t = typeof t.then == "function" ? su(t) : t, l = l.useState()[0], (ol !== null ? ol.memoizedState : null) !== l && (L.flags |= 1024), t;
  }
  function ki() {
    var l = mn !== 0;
    return mn = 0, l;
  }
  function Fi(l, t, a) {
    t.updateQueue = l.updateQueue, t.flags &= -2053, l.lanes &= ~a;
  }
  function Ii(l) {
    if (on) {
      for (l = l.memoizedState; l !== null; ) {
        var t = l.queue;
        t !== null && (t.pending = null), l = l.next;
      }
      on = !1;
    }
    Wt = 0, Ol = ol = L = null, Ee = !1, fu = mn = 0, Ae = null;
  }
  function Il() {
    var l = {
      memoizedState: null,
      baseState: null,
      baseQueue: null,
      queue: null,
      next: null
    };
    return Ol === null ? L.memoizedState = Ol = l : Ol = Ol.next = l, Ol;
  }
  function Nl() {
    if (ol === null) {
      var l = L.alternate;
      l = l !== null ? l.memoizedState : null;
    } else l = ol.next;
    var t = Ol === null ? L.memoizedState : Ol.next;
    if (t !== null)
      Ol = t, ol = l;
    else {
      if (l === null)
        throw L.alternate === null ? Error(g(467)) : Error(g(310));
      ol = l, l = {
        memoizedState: ol.memoizedState,
        baseState: ol.baseState,
        baseQueue: ol.baseQueue,
        queue: ol.queue,
        next: null
      }, Ol === null ? L.memoizedState = Ol = l : Ol = Ol.next = l;
    }
    return Ol;
  }
  function hn() {
    return { lastEffect: null, events: null, stores: null, memoCache: null };
  }
  function su(l) {
    var t = fu;
    return fu += 1, Ae === null && (Ae = []), l = Hs(Ae, l, t), t = L, (Ol === null ? t.memoizedState : Ol.next) === null && (t = t.alternate, b.H = t === null || t.memoizedState === null ? Td : dc), l;
  }
  function vn(l) {
    if (l !== null && typeof l == "object") {
      if (typeof l.then == "function") return su(l);
      if (l.$$typeof === Bl) return Jl(l);
    }
    throw Error(g(438, String(l)));
  }
  function Pi(l) {
    var t = null, a = L.updateQueue;
    if (a !== null && (t = a.memoCache), t == null) {
      var e = L.alternate;
      e !== null && (e = e.updateQueue, e !== null && (e = e.memoCache, e != null && (t = {
        data: e.data.map(function(u) {
          return u.slice();
        }),
        index: 0
      })));
    }
    if (t == null && (t = { data: [], index: 0 }), a === null && (a = hn(), L.updateQueue = a), a.memoCache = t, a = t.data[t.index], a === void 0)
      for (a = t.data[t.index] = Array(l), e = 0; e < l; e++)
        a[e] = Zt;
    return t.index++, a;
  }
  function kt(l, t) {
    return typeof t == "function" ? t(l) : t;
  }
  function yn(l) {
    var t = Nl();
    return lc(t, ol, l);
  }
  function lc(l, t, a) {
    var e = l.queue;
    if (e === null) throw Error(g(311));
    e.lastRenderedReducer = a;
    var u = l.baseQueue, n = e.pending;
    if (n !== null) {
      if (u !== null) {
        var i = u.next;
        u.next = n.next, n.next = i;
      }
      t.baseQueue = u = n, e.pending = null;
    }
    if (n = l.baseState, u === null) l.memoizedState = n;
    else {
      t = u.next;
      var c = i = null, s = null, y = t, z = !1;
      do {
        var T = y.lane & -536870913;
        if (T !== y.lane ? (k & T) === T : (Wt & T) === T) {
          var r = y.revertLane;
          if (r === 0)
            s !== null && (s = s.next = {
              lane: 0,
              revertLane: 0,
              gesture: null,
              action: y.action,
              hasEagerState: y.hasEagerState,
              eagerState: y.eagerState,
              next: null
            }), T === ge && (z = !0);
          else if ((Wt & r) === r) {
            y = y.next, r === ge && (z = !0);
            continue;
          } else
            T = {
              lane: 0,
              revertLane: y.revertLane,
              gesture: null,
              action: y.action,
              hasEagerState: y.hasEagerState,
              eagerState: y.eagerState,
              next: null
            }, s === null ? (c = s = T, i = n) : s = s.next = T, L.lanes |= r, pa |= r;
          T = y.action, $a && a(n, T), n = y.hasEagerState ? y.eagerState : a(n, T);
        } else
          r = {
            lane: T,
            revertLane: y.revertLane,
            gesture: y.gesture,
            action: y.action,
            hasEagerState: y.hasEagerState,
            eagerState: y.eagerState,
            next: null
          }, s === null ? (c = s = r, i = n) : s = s.next = r, L.lanes |= T, pa |= T;
        y = y.next;
      } while (y !== null && y !== t);
      if (s === null ? i = n : s.next = c, !st(n, l.memoizedState) && (Dl = !0, z && (a = Se, a !== null)))
        throw a;
      l.memoizedState = n, l.baseState = i, l.baseQueue = s, e.lastRenderedState = n;
    }
    return u === null && (e.lanes = 0), [l.memoizedState, e.dispatch];
  }
  function tc(l) {
    var t = Nl(), a = t.queue;
    if (a === null) throw Error(g(311));
    a.lastRenderedReducer = l;
    var e = a.dispatch, u = a.pending, n = t.memoizedState;
    if (u !== null) {
      a.pending = null;
      var i = u = u.next;
      do
        n = l(n, i.action), i = i.next;
      while (i !== u);
      st(n, t.memoizedState) || (Dl = !0), t.memoizedState = n, t.baseQueue === null && (t.baseState = n), a.lastRenderedState = n;
    }
    return [n, e];
  }
  function Vs(l, t, a) {
    var e = L, u = Nl(), n = I;
    if (n) {
      if (a === void 0) throw Error(g(407));
      a = a();
    } else a = t();
    var i = !st(
      (ol || u).memoizedState,
      a
    );
    if (i && (u.memoizedState = a, Dl = !0), u = u.queue, uc(ws.bind(null, e, u, l), [
      l
    ]), u.getSnapshot !== t || i || Ol !== null && Ol.memoizedState.tag & 1) {
      if (e.flags |= 2048, Te(
        9,
        { destroy: void 0 },
        Js.bind(
          null,
          e,
          u,
          a,
          t
        ),
        null
      ), vl === null) throw Error(g(349));
      n || (Wt & 127) !== 0 || Ks(e, t, a);
    }
    return a;
  }
  function Ks(l, t, a) {
    l.flags |= 16384, l = { getSnapshot: t, value: a }, t = L.updateQueue, t === null ? (t = hn(), L.updateQueue = t, t.stores = [l]) : (a = t.stores, a === null ? t.stores = [l] : a.push(l));
  }
  function Js(l, t, a, e) {
    t.value = a, t.getSnapshot = e, $s(t) && Ws(l);
  }
  function ws(l, t, a) {
    return a(function() {
      $s(t) && Ws(l);
    });
  }
  function $s(l) {
    var t = l.getSnapshot;
    l = l.value;
    try {
      var a = t();
      return !st(l, a);
    } catch {
      return !0;
    }
  }
  function Ws(l) {
    var t = Ga(l, 2);
    t !== null && it(t, l, 2);
  }
  function ac(l) {
    var t = Il();
    if (typeof l == "function") {
      var a = l;
      if (l = a(), $a) {
        ia(!0);
        try {
          a();
        } finally {
          ia(!1);
        }
      }
    }
    return t.memoizedState = t.baseState = l, t.queue = {
      pending: null,
      lanes: 0,
      dispatch: null,
      lastRenderedReducer: kt,
      lastRenderedState: l
    }, t;
  }
  function ks(l, t, a, e) {
    return l.baseState = a, lc(
      l,
      ol,
      typeof e == "function" ? e : kt
    );
  }
  function Kh(l, t, a, e, u) {
    if (Sn(l)) throw Error(g(485));
    if (l = t.action, l !== null) {
      var n = {
        payload: u,
        action: l,
        next: null,
        isTransition: !0,
        status: "pending",
        value: null,
        reason: null,
        listeners: [],
        then: function(i) {
          n.listeners.push(i);
        }
      };
      b.T !== null ? a(!0) : n.isTransition = !1, e(n), a = t.pending, a === null ? (n.next = t.pending = n, Fs(t, n)) : (n.next = a.next, t.pending = a.next = n);
    }
  }
  function Fs(l, t) {
    var a = t.action, e = t.payload, u = l.state;
    if (t.isTransition) {
      var n = b.T, i = {};
      b.T = i;
      try {
        var c = a(u, e), s = b.S;
        s !== null && s(i, c), Is(l, t, c);
      } catch (y) {
        ec(l, t, y);
      } finally {
        n !== null && i.types !== null && (n.types = i.types), b.T = n;
      }
    } else
      try {
        n = a(u, e), Is(l, t, n);
      } catch (y) {
        ec(l, t, y);
      }
  }
  function Is(l, t, a) {
    a !== null && typeof a == "object" && typeof a.then == "function" ? a.then(
      function(e) {
        Ps(l, t, e);
      },
      function(e) {
        return ec(l, t, e);
      }
    ) : Ps(l, t, a);
  }
  function Ps(l, t, a) {
    t.status = "fulfilled", t.value = a, ld(t), l.state = a, t = l.pending, t !== null && (a = t.next, a === t ? l.pending = null : (a = a.next, t.next = a, Fs(l, a)));
  }
  function ec(l, t, a) {
    var e = l.pending;
    if (l.pending = null, e !== null) {
      e = e.next;
      do
        t.status = "rejected", t.reason = a, ld(t), t = t.next;
      while (t !== e);
    }
    l.action = null;
  }
  function ld(l) {
    l = l.listeners;
    for (var t = 0; t < l.length; t++) (0, l[t])();
  }
  function td(l, t) {
    return t;
  }
  function ad(l, t) {
    if (I) {
      var a = vl.formState;
      if (a !== null) {
        l: {
          var e = L;
          if (I) {
            if (rl) {
              t: {
                for (var u = rl, n = jt; u.nodeType !== 8; ) {
                  if (!n) {
                    u = null;
                    break t;
                  }
                  if (u = Nt(
                    u.nextSibling
                  ), u === null) {
                    u = null;
                    break t;
                  }
                }
                n = u.data, u = n === "F!" || n === "F" ? u : null;
              }
              if (u) {
                rl = Nt(
                  u.nextSibling
                ), e = u.data === "F!";
                break l;
              }
            }
            oa(e);
          }
          e = !1;
        }
        e && (t = a[0]);
      }
    }
    return a = Il(), a.memoizedState = a.baseState = t, e = {
      pending: null,
      lanes: 0,
      dispatch: null,
      lastRenderedReducer: td,
      lastRenderedState: t
    }, a.queue = e, a = zd.bind(
      null,
      L,
      e
    ), e.dispatch = a, e = ac(!1), n = sc.bind(
      null,
      L,
      !1,
      e.queue
    ), e = Il(), u = {
      state: t,
      dispatch: null,
      action: l,
      pending: null
    }, e.queue = u, a = Kh.bind(
      null,
      L,
      u,
      n,
      a
    ), u.dispatch = a, e.memoizedState = l, [t, a, !1];
  }
  function ed(l) {
    var t = Nl();
    return ud(t, ol, l);
  }
  function ud(l, t, a) {
    if (t = lc(
      l,
      t,
      td
    )[0], l = yn(kt)[0], typeof t == "object" && t !== null && typeof t.then == "function")
      try {
        var e = su(t);
      } catch (i) {
        throw i === be ? un : i;
      }
    else e = t;
    t = Nl();
    var u = t.queue, n = u.dispatch;
    return a !== t.memoizedState && (L.flags |= 2048, Te(
      9,
      { destroy: void 0 },
      Jh.bind(null, u, a),
      null
    )), [e, n, l];
  }
  function Jh(l, t) {
    l.action = t;
  }
  function nd(l) {
    var t = Nl(), a = ol;
    if (a !== null)
      return ud(t, a, l);
    Nl(), t = t.memoizedState, a = Nl();
    var e = a.queue.dispatch;
    return a.memoizedState = l, [t, e, !1];
  }
  function Te(l, t, a, e) {
    return l = { tag: l, create: a, deps: e, inst: t, next: null }, t = L.updateQueue, t === null && (t = hn(), L.updateQueue = t), a = t.lastEffect, a === null ? t.lastEffect = l.next = l : (e = a.next, a.next = l, l.next = e, t.lastEffect = l), l;
  }
  function id() {
    return Nl().memoizedState;
  }
  function rn(l, t, a, e) {
    var u = Il();
    L.flags |= l, u.memoizedState = Te(
      1 | t,
      { destroy: void 0 },
      a,
      e === void 0 ? null : e
    );
  }
  function gn(l, t, a, e) {
    var u = Nl();
    e = e === void 0 ? null : e;
    var n = u.memoizedState.inst;
    ol !== null && e !== null && $i(e, ol.memoizedState.deps) ? u.memoizedState = Te(t, n, a, e) : (L.flags |= l, u.memoizedState = Te(
      1 | t,
      n,
      a,
      e
    ));
  }
  function cd(l, t) {
    rn(8390656, 8, l, t);
  }
  function uc(l, t) {
    gn(2048, 8, l, t);
  }
  function wh(l) {
    L.flags |= 4;
    var t = L.updateQueue;
    if (t === null)
      t = hn(), L.updateQueue = t, t.events = [l];
    else {
      var a = t.events;
      a === null ? t.events = [l] : a.push(l);
    }
  }
  function fd(l) {
    var t = Nl().memoizedState;
    return wh({ ref: t, nextImpl: l }), function() {
      if ((il & 2) !== 0) throw Error(g(440));
      return t.impl.apply(void 0, arguments);
    };
  }
  function sd(l, t) {
    return gn(4, 2, l, t);
  }
  function dd(l, t) {
    return gn(4, 4, l, t);
  }
  function od(l, t) {
    if (typeof t == "function") {
      l = l();
      var a = t(l);
      return function() {
        typeof a == "function" ? a() : t(null);
      };
    }
    if (t != null)
      return l = l(), t.current = l, function() {
        t.current = null;
      };
  }
  function md(l, t, a) {
    a = a != null ? a.concat([l]) : null, gn(4, 4, od.bind(null, t, l), a);
  }
  function nc() {
  }
  function hd(l, t) {
    var a = Nl();
    t = t === void 0 ? null : t;
    var e = a.memoizedState;
    return t !== null && $i(t, e[1]) ? e[0] : (a.memoizedState = [l, t], l);
  }
  function vd(l, t) {
    var a = Nl();
    t = t === void 0 ? null : t;
    var e = a.memoizedState;
    if (t !== null && $i(t, e[1]))
      return e[0];
    if (e = l(), $a) {
      ia(!0);
      try {
        l();
      } finally {
        ia(!1);
      }
    }
    return a.memoizedState = [e, t], e;
  }
  function ic(l, t, a) {
    return a === void 0 || (Wt & 1073741824) !== 0 && (k & 261930) === 0 ? l.memoizedState = t : (l.memoizedState = a, l = ro(), L.lanes |= l, pa |= l, a);
  }
  function yd(l, t, a, e) {
    return st(a, t) ? a : ze.current !== null ? (l = ic(l, a, e), st(l, t) || (Dl = !0), l) : (Wt & 42) === 0 || (Wt & 1073741824) !== 0 && (k & 261930) === 0 ? (Dl = !0, l.memoizedState = a) : (l = ro(), L.lanes |= l, pa |= l, t);
  }
  function rd(l, t, a, e, u) {
    var n = _.p;
    _.p = n !== 0 && 8 > n ? n : 8;
    var i = b.T, c = {};
    b.T = c, sc(l, !1, t, a);
    try {
      var s = u(), y = b.S;
      if (y !== null && y(c, s), s !== null && typeof s == "object" && typeof s.then == "function") {
        var z = Qh(
          s,
          e
        );
        du(
          l,
          t,
          z,
          yt(l)
        );
      } else
        du(
          l,
          t,
          e,
          yt(l)
        );
    } catch (T) {
      du(
        l,
        t,
        { then: function() {
        }, status: "rejected", reason: T },
        yt()
      );
    } finally {
      _.p = n, i !== null && c.types !== null && (i.types = c.types), b.T = i;
    }
  }
  function $h() {
  }
  function cc(l, t, a, e) {
    if (l.tag !== 5) throw Error(g(476));
    var u = gd(l).queue;
    rd(
      l,
      u,
      t,
      Y,
      a === null ? $h : function() {
        return Sd(l), a(e);
      }
    );
  }
  function gd(l) {
    var t = l.memoizedState;
    if (t !== null) return t;
    t = {
      memoizedState: Y,
      baseState: Y,
      baseQueue: null,
      queue: {
        pending: null,
        lanes: 0,
        dispatch: null,
        lastRenderedReducer: kt,
        lastRenderedState: Y
      },
      next: null
    };
    var a = {};
    return t.next = {
      memoizedState: a,
      baseState: a,
      baseQueue: null,
      queue: {
        pending: null,
        lanes: 0,
        dispatch: null,
        lastRenderedReducer: kt,
        lastRenderedState: a
      },
      next: null
    }, l.memoizedState = t, l = l.alternate, l !== null && (l.memoizedState = t), t;
  }
  function Sd(l) {
    var t = gd(l);
    t.next === null && (t = l.alternate.memoizedState), du(
      l,
      t.next.queue,
      {},
      yt()
    );
  }
  function fc() {
    return Jl(_u);
  }
  function bd() {
    return Nl().memoizedState;
  }
  function pd() {
    return Nl().memoizedState;
  }
  function Wh(l) {
    for (var t = l.return; t !== null; ) {
      switch (t.tag) {
        case 24:
        case 3:
          var a = yt();
          l = va(a);
          var e = ya(t, l, a);
          e !== null && (it(e, t, a), nu(e, t, a)), t = { cache: Yi() }, l.payload = t;
          return;
      }
      t = t.return;
    }
  }
  function kh(l, t, a) {
    var e = yt();
    a = {
      lane: e,
      revertLane: 0,
      gesture: null,
      action: a,
      hasEagerState: !1,
      eagerState: null,
      next: null
    }, Sn(l) ? Ed(t, a) : (a = ji(l, t, a, e), a !== null && (it(a, l, e), Ad(a, t, e)));
  }
  function zd(l, t, a) {
    var e = yt();
    du(l, t, a, e);
  }
  function du(l, t, a, e) {
    var u = {
      lane: e,
      revertLane: 0,
      gesture: null,
      action: a,
      hasEagerState: !1,
      eagerState: null,
      next: null
    };
    if (Sn(l)) Ed(t, u);
    else {
      var n = l.alternate;
      if (l.lanes === 0 && (n === null || n.lanes === 0) && (n = t.lastRenderedReducer, n !== null))
        try {
          var i = t.lastRenderedState, c = n(i, a);
          if (u.hasEagerState = !0, u.eagerState = c, st(c, i))
            return Fu(l, t, u, 0), vl === null && ku(), !1;
        } catch {
        }
      if (a = ji(l, t, u, e), a !== null)
        return it(a, l, e), Ad(a, t, e), !0;
    }
    return !1;
  }
  function sc(l, t, a, e) {
    if (e = {
      lane: 2,
      revertLane: Zc(),
      gesture: null,
      action: e,
      hasEagerState: !1,
      eagerState: null,
      next: null
    }, Sn(l)) {
      if (t) throw Error(g(479));
    } else
      t = ji(
        l,
        a,
        e,
        2
      ), t !== null && it(t, l, 2);
  }
  function Sn(l) {
    var t = l.alternate;
    return l === L || t !== null && t === L;
  }
  function Ed(l, t) {
    Ee = on = !0;
    var a = l.pending;
    a === null ? t.next = t : (t.next = a.next, a.next = t), l.pending = t;
  }
  function Ad(l, t, a) {
    if ((a & 4194048) !== 0) {
      var e = t.lanes;
      e &= l.pendingLanes, a |= e, t.lanes = a, Nf(l, a);
    }
  }
  var ou = {
    readContext: Jl,
    use: vn,
    useCallback: Tl,
    useContext: Tl,
    useEffect: Tl,
    useImperativeHandle: Tl,
    useLayoutEffect: Tl,
    useInsertionEffect: Tl,
    useMemo: Tl,
    useReducer: Tl,
    useRef: Tl,
    useState: Tl,
    useDebugValue: Tl,
    useDeferredValue: Tl,
    useTransition: Tl,
    useSyncExternalStore: Tl,
    useId: Tl,
    useHostTransitionStatus: Tl,
    useFormState: Tl,
    useActionState: Tl,
    useOptimistic: Tl,
    useMemoCache: Tl,
    useCacheRefresh: Tl
  };
  ou.useEffectEvent = Tl;
  var Td = {
    readContext: Jl,
    use: vn,
    useCallback: function(l, t) {
      return Il().memoizedState = [
        l,
        t === void 0 ? null : t
      ], l;
    },
    useContext: Jl,
    useEffect: cd,
    useImperativeHandle: function(l, t, a) {
      a = a != null ? a.concat([l]) : null, rn(
        4194308,
        4,
        od.bind(null, t, l),
        a
      );
    },
    useLayoutEffect: function(l, t) {
      return rn(4194308, 4, l, t);
    },
    useInsertionEffect: function(l, t) {
      rn(4, 2, l, t);
    },
    useMemo: function(l, t) {
      var a = Il();
      t = t === void 0 ? null : t;
      var e = l();
      if ($a) {
        ia(!0);
        try {
          l();
        } finally {
          ia(!1);
        }
      }
      return a.memoizedState = [e, t], e;
    },
    useReducer: function(l, t, a) {
      var e = Il();
      if (a !== void 0) {
        var u = a(t);
        if ($a) {
          ia(!0);
          try {
            a(t);
          } finally {
            ia(!1);
          }
        }
      } else u = t;
      return e.memoizedState = e.baseState = u, l = {
        pending: null,
        lanes: 0,
        dispatch: null,
        lastRenderedReducer: l,
        lastRenderedState: u
      }, e.queue = l, l = l.dispatch = kh.bind(
        null,
        L,
        l
      ), [e.memoizedState, l];
    },
    useRef: function(l) {
      var t = Il();
      return l = { current: l }, t.memoizedState = l;
    },
    useState: function(l) {
      l = ac(l);
      var t = l.queue, a = zd.bind(null, L, t);
      return t.dispatch = a, [l.memoizedState, a];
    },
    useDebugValue: nc,
    useDeferredValue: function(l, t) {
      var a = Il();
      return ic(a, l, t);
    },
    useTransition: function() {
      var l = ac(!1);
      return l = rd.bind(
        null,
        L,
        l.queue,
        !0,
        !1
      ), Il().memoizedState = l, [!1, l];
    },
    useSyncExternalStore: function(l, t, a) {
      var e = L, u = Il();
      if (I) {
        if (a === void 0)
          throw Error(g(407));
        a = a();
      } else {
        if (a = t(), vl === null)
          throw Error(g(349));
        (k & 127) !== 0 || Ks(e, t, a);
      }
      u.memoizedState = a;
      var n = { value: a, getSnapshot: t };
      return u.queue = n, cd(ws.bind(null, e, n, l), [
        l
      ]), e.flags |= 2048, Te(
        9,
        { destroy: void 0 },
        Js.bind(
          null,
          e,
          n,
          a,
          t
        ),
        null
      ), a;
    },
    useId: function() {
      var l = Il(), t = vl.identifierPrefix;
      if (I) {
        var a = Bt, e = Yt;
        a = (e & ~(1 << 32 - ft(e) - 1)).toString(32) + a, t = "_" + t + "R_" + a, a = mn++, 0 < a && (t += "H" + a.toString(32)), t += "_";
      } else
        a = Lh++, t = "_" + t + "r_" + a.toString(32) + "_";
      return l.memoizedState = t;
    },
    useHostTransitionStatus: fc,
    useFormState: ad,
    useActionState: ad,
    useOptimistic: function(l) {
      var t = Il();
      t.memoizedState = t.baseState = l;
      var a = {
        pending: null,
        lanes: 0,
        dispatch: null,
        lastRenderedReducer: null,
        lastRenderedState: null
      };
      return t.queue = a, t = sc.bind(
        null,
        L,
        !0,
        a
      ), a.dispatch = t, [l, t];
    },
    useMemoCache: Pi,
    useCacheRefresh: function() {
      return Il().memoizedState = Wh.bind(
        null,
        L
      );
    },
    useEffectEvent: function(l) {
      var t = Il(), a = { impl: l };
      return t.memoizedState = a, function() {
        if ((il & 2) !== 0)
          throw Error(g(440));
        return a.impl.apply(void 0, arguments);
      };
    }
  }, dc = {
    readContext: Jl,
    use: vn,
    useCallback: hd,
    useContext: Jl,
    useEffect: uc,
    useImperativeHandle: md,
    useInsertionEffect: sd,
    useLayoutEffect: dd,
    useMemo: vd,
    useReducer: yn,
    useRef: id,
    useState: function() {
      return yn(kt);
    },
    useDebugValue: nc,
    useDeferredValue: function(l, t) {
      var a = Nl();
      return yd(
        a,
        ol.memoizedState,
        l,
        t
      );
    },
    useTransition: function() {
      var l = yn(kt)[0], t = Nl().memoizedState;
      return [
        typeof l == "boolean" ? l : su(l),
        t
      ];
    },
    useSyncExternalStore: Vs,
    useId: bd,
    useHostTransitionStatus: fc,
    useFormState: ed,
    useActionState: ed,
    useOptimistic: function(l, t) {
      var a = Nl();
      return ks(a, ol, l, t);
    },
    useMemoCache: Pi,
    useCacheRefresh: pd
  };
  dc.useEffectEvent = fd;
  var xd = {
    readContext: Jl,
    use: vn,
    useCallback: hd,
    useContext: Jl,
    useEffect: uc,
    useImperativeHandle: md,
    useInsertionEffect: sd,
    useLayoutEffect: dd,
    useMemo: vd,
    useReducer: tc,
    useRef: id,
    useState: function() {
      return tc(kt);
    },
    useDebugValue: nc,
    useDeferredValue: function(l, t) {
      var a = Nl();
      return ol === null ? ic(a, l, t) : yd(
        a,
        ol.memoizedState,
        l,
        t
      );
    },
    useTransition: function() {
      var l = tc(kt)[0], t = Nl().memoizedState;
      return [
        typeof l == "boolean" ? l : su(l),
        t
      ];
    },
    useSyncExternalStore: Vs,
    useId: bd,
    useHostTransitionStatus: fc,
    useFormState: nd,
    useActionState: nd,
    useOptimistic: function(l, t) {
      var a = Nl();
      return ol !== null ? ks(a, ol, l, t) : (a.baseState = l, [l, a.queue.dispatch]);
    },
    useMemoCache: Pi,
    useCacheRefresh: pd
  };
  xd.useEffectEvent = fd;
  function oc(l, t, a, e) {
    t = l.memoizedState, a = a(e, t), a = a == null ? t : C({}, t, a), l.memoizedState = a, l.lanes === 0 && (l.updateQueue.baseState = a);
  }
  var mc = {
    enqueueSetState: function(l, t, a) {
      l = l._reactInternals;
      var e = yt(), u = va(e);
      u.payload = t, a != null && (u.callback = a), t = ya(l, u, e), t !== null && (it(t, l, e), nu(t, l, e));
    },
    enqueueReplaceState: function(l, t, a) {
      l = l._reactInternals;
      var e = yt(), u = va(e);
      u.tag = 1, u.payload = t, a != null && (u.callback = a), t = ya(l, u, e), t !== null && (it(t, l, e), nu(t, l, e));
    },
    enqueueForceUpdate: function(l, t) {
      l = l._reactInternals;
      var a = yt(), e = va(a);
      e.tag = 2, t != null && (e.callback = t), t = ya(l, e, a), t !== null && (it(t, l, a), nu(t, l, a));
    }
  };
  function jd(l, t, a, e, u, n, i) {
    return l = l.stateNode, typeof l.shouldComponentUpdate == "function" ? l.shouldComponentUpdate(e, n, i) : t.prototype && t.prototype.isPureReactComponent ? !Fe(a, e) || !Fe(u, n) : !0;
  }
  function _d(l, t, a, e) {
    l = t.state, typeof t.componentWillReceiveProps == "function" && t.componentWillReceiveProps(a, e), typeof t.UNSAFE_componentWillReceiveProps == "function" && t.UNSAFE_componentWillReceiveProps(a, e), t.state !== l && mc.enqueueReplaceState(t, t.state, null);
  }
  function Wa(l, t) {
    var a = t;
    if ("ref" in t) {
      a = {};
      for (var e in t)
        e !== "ref" && (a[e] = t[e]);
    }
    if (l = l.defaultProps) {
      a === t && (a = C({}, a));
      for (var u in l)
        a[u] === void 0 && (a[u] = l[u]);
    }
    return a;
  }
  function Nd(l) {
    Wu(l);
  }
  function Md(l) {
    console.error(l);
  }
  function Od(l) {
    Wu(l);
  }
  function bn(l, t) {
    try {
      var a = l.onUncaughtError;
      a(t.value, { componentStack: t.stack });
    } catch (e) {
      setTimeout(function() {
        throw e;
      });
    }
  }
  function Dd(l, t, a) {
    try {
      var e = l.onCaughtError;
      e(a.value, {
        componentStack: a.stack,
        errorBoundary: t.tag === 1 ? t.stateNode : null
      });
    } catch (u) {
      setTimeout(function() {
        throw u;
      });
    }
  }
  function hc(l, t, a) {
    return a = va(a), a.tag = 3, a.payload = { element: null }, a.callback = function() {
      bn(l, t);
    }, a;
  }
  function Ud(l) {
    return l = va(l), l.tag = 3, l;
  }
  function Hd(l, t, a, e) {
    var u = a.type.getDerivedStateFromError;
    if (typeof u == "function") {
      var n = e.value;
      l.payload = function() {
        return u(n);
      }, l.callback = function() {
        Dd(t, a, e);
      };
    }
    var i = a.stateNode;
    i !== null && typeof i.componentDidCatch == "function" && (l.callback = function() {
      Dd(t, a, e), typeof u != "function" && (za === null ? za = /* @__PURE__ */ new Set([this]) : za.add(this));
      var c = e.stack;
      this.componentDidCatch(e.value, {
        componentStack: c !== null ? c : ""
      });
    });
  }
  function Fh(l, t, a, e, u) {
    if (a.flags |= 32768, e !== null && typeof e == "object" && typeof e.then == "function") {
      if (t = a.alternate, t !== null && re(
        t,
        a,
        u,
        !0
      ), a = ot.current, a !== null) {
        switch (a.tag) {
          case 31:
          case 13:
            return _t === null ? Dn() : a.alternate === null && xl === 0 && (xl = 3), a.flags &= -257, a.flags |= 65536, a.lanes = u, e === nn ? a.flags |= 16384 : (t = a.updateQueue, t === null ? a.updateQueue = /* @__PURE__ */ new Set([e]) : t.add(e), Bc(l, e, u)), !1;
          case 22:
            return a.flags |= 65536, e === nn ? a.flags |= 16384 : (t = a.updateQueue, t === null ? (t = {
              transitions: null,
              markerInstances: null,
              retryQueue: /* @__PURE__ */ new Set([e])
            }, a.updateQueue = t) : (a = t.retryQueue, a === null ? t.retryQueue = /* @__PURE__ */ new Set([e]) : a.add(e)), Bc(l, e, u)), !1;
        }
        throw Error(g(435, a.tag));
      }
      return Bc(l, e, u), Dn(), !1;
    }
    if (I)
      return t = ot.current, t !== null ? ((t.flags & 65536) === 0 && (t.flags |= 256), t.flags |= 65536, t.lanes = u, e !== Ui && (l = Error(g(422), { cause: e }), lu(At(l, a)))) : (e !== Ui && (t = Error(g(423), {
        cause: e
      }), lu(
        At(t, a)
      )), l = l.current.alternate, l.flags |= 65536, u &= -u, l.lanes |= u, e = At(e, a), u = hc(
        l.stateNode,
        e,
        u
      ), Li(l, u), xl !== 4 && (xl = 2)), !1;
    var n = Error(g(520), { cause: e });
    if (n = At(n, a), bu === null ? bu = [n] : bu.push(n), xl !== 4 && (xl = 2), t === null) return !0;
    e = At(e, a), a = t;
    do {
      switch (a.tag) {
        case 3:
          return a.flags |= 65536, l = u & -u, a.lanes |= l, l = hc(a.stateNode, e, l), Li(a, l), !1;
        case 1:
          if (t = a.type, n = a.stateNode, (a.flags & 128) === 0 && (typeof t.getDerivedStateFromError == "function" || n !== null && typeof n.componentDidCatch == "function" && (za === null || !za.has(n))))
            return a.flags |= 65536, u &= -u, a.lanes |= u, u = Ud(u), Hd(
              u,
              l,
              a,
              e
            ), Li(a, u), !1;
      }
      a = a.return;
    } while (a !== null);
    return !1;
  }
  var vc = Error(g(461)), Dl = !1;
  function wl(l, t, a, e) {
    t.child = l === null ? Ys(t, null, a, e) : wa(
      t,
      l.child,
      a,
      e
    );
  }
  function Cd(l, t, a, e, u) {
    a = a.render;
    var n = t.ref;
    if ("ref" in e) {
      var i = {};
      for (var c in e)
        c !== "ref" && (i[c] = e[c]);
    } else i = e;
    return La(t), e = Wi(
      l,
      t,
      a,
      i,
      n,
      u
    ), c = ki(), l !== null && !Dl ? (Fi(l, t, u), Ft(l, t, u)) : (I && c && Oi(t), t.flags |= 1, wl(l, t, e, u), t.child);
  }
  function Rd(l, t, a, e, u) {
    if (l === null) {
      var n = a.type;
      return typeof n == "function" && !_i(n) && n.defaultProps === void 0 && a.compare === null ? (t.tag = 15, t.type = n, qd(
        l,
        t,
        n,
        e,
        u
      )) : (l = Pu(
        a.type,
        null,
        e,
        t,
        t.mode,
        u
      ), l.ref = t.ref, l.return = t, t.child = l);
    }
    if (n = l.child, !Ec(l, u)) {
      var i = n.memoizedProps;
      if (a = a.compare, a = a !== null ? a : Fe, a(i, e) && l.ref === t.ref)
        return Ft(l, t, u);
    }
    return t.flags |= 1, l = Kt(n, e), l.ref = t.ref, l.return = t, t.child = l;
  }
  function qd(l, t, a, e, u) {
    if (l !== null) {
      var n = l.memoizedProps;
      if (Fe(n, e) && l.ref === t.ref)
        if (Dl = !1, t.pendingProps = e = n, Ec(l, u))
          (l.flags & 131072) !== 0 && (Dl = !0);
        else
          return t.lanes = l.lanes, Ft(l, t, u);
    }
    return yc(
      l,
      t,
      a,
      e,
      u
    );
  }
  function Yd(l, t, a, e) {
    var u = e.children, n = l !== null ? l.memoizedState : null;
    if (l === null && t.stateNode === null && (t.stateNode = {
      _visibility: 1,
      _pendingMarkers: null,
      _retryCache: null,
      _transitions: null
    }), e.mode === "hidden") {
      if ((t.flags & 128) !== 0) {
        if (n = n !== null ? n.baseLanes | a : a, l !== null) {
          for (e = t.child = l.child, u = 0; e !== null; )
            u = u | e.lanes | e.childLanes, e = e.sibling;
          e = u & ~n;
        } else e = 0, t.child = null;
        return Bd(
          l,
          t,
          n,
          a,
          e
        );
      }
      if ((a & 536870912) !== 0)
        t.memoizedState = { baseLanes: 0, cachePool: null }, l !== null && en(
          t,
          n !== null ? n.cachePool : null
        ), n !== null ? Xs(t, n) : Ki(), Zs(t);
      else
        return e = t.lanes = 536870912, Bd(
          l,
          t,
          n !== null ? n.baseLanes | a : a,
          a,
          e
        );
    } else
      n !== null ? (en(t, n.cachePool), Xs(t, n), ga(), t.memoizedState = null) : (l !== null && en(t, null), Ki(), ga());
    return wl(l, t, u, a), t.child;
  }
  function mu(l, t) {
    return l !== null && l.tag === 22 || t.stateNode !== null || (t.stateNode = {
      _visibility: 1,
      _pendingMarkers: null,
      _retryCache: null,
      _transitions: null
    }), t.sibling;
  }
  function Bd(l, t, a, e, u) {
    var n = Gi();
    return n = n === null ? null : { parent: Ml._currentValue, pool: n }, t.memoizedState = {
      baseLanes: a,
      cachePool: n
    }, l !== null && en(t, null), Ki(), Zs(t), l !== null && re(l, t, e, !0), t.childLanes = u, null;
  }
  function pn(l, t) {
    return t = En(
      { mode: t.mode, children: t.children },
      l.mode
    ), t.ref = l.ref, l.child = t, t.return = l, t;
  }
  function Gd(l, t, a) {
    return wa(t, l.child, null, a), l = pn(t, t.pendingProps), l.flags |= 2, mt(t), t.memoizedState = null, l;
  }
  function Ih(l, t, a) {
    var e = t.pendingProps, u = (t.flags & 128) !== 0;
    if (t.flags &= -129, l === null) {
      if (I) {
        if (e.mode === "hidden")
          return l = pn(t, e), t.lanes = 536870912, mu(null, l);
        if (wi(t), (l = rl) ? (l = Io(
          l,
          jt
        ), l = l !== null && l.data === "&" ? l : null, l !== null && (t.memoizedState = {
          dehydrated: l,
          treeContext: sa !== null ? { id: Yt, overflow: Bt } : null,
          retryLane: 536870912,
          hydrationErrors: null
        }, a = Es(l), a.return = t, t.child = a, Kl = t, rl = null)) : l = null, l === null) throw oa(t);
        return t.lanes = 536870912, null;
      }
      return pn(t, e);
    }
    var n = l.memoizedState;
    if (n !== null) {
      var i = n.dehydrated;
      if (wi(t), u)
        if (t.flags & 256)
          t.flags &= -257, t = Gd(
            l,
            t,
            a
          );
        else if (t.memoizedState !== null)
          t.child = l.child, t.flags |= 128, t = null;
        else throw Error(g(558));
      else if (Dl || re(l, t, a, !1), u = (a & l.childLanes) !== 0, Dl || u) {
        if (e = vl, e !== null && (i = Mf(e, a), i !== 0 && i !== n.retryLane))
          throw n.retryLane = i, Ga(l, i), it(e, l, i), vc;
        Dn(), t = Gd(
          l,
          t,
          a
        );
      } else
        l = n.treeContext, rl = Nt(i.nextSibling), Kl = t, I = !0, da = null, jt = !1, l !== null && xs(t, l), t = pn(t, e), t.flags |= 4096;
      return t;
    }
    return l = Kt(l.child, {
      mode: e.mode,
      children: e.children
    }), l.ref = t.ref, t.child = l, l.return = t, l;
  }
  function zn(l, t) {
    var a = t.ref;
    if (a === null)
      l !== null && l.ref !== null && (t.flags |= 4194816);
    else {
      if (typeof a != "function" && typeof a != "object")
        throw Error(g(284));
      (l === null || l.ref !== a) && (t.flags |= 4194816);
    }
  }
  function yc(l, t, a, e, u) {
    return La(t), a = Wi(
      l,
      t,
      a,
      e,
      void 0,
      u
    ), e = ki(), l !== null && !Dl ? (Fi(l, t, u), Ft(l, t, u)) : (I && e && Oi(t), t.flags |= 1, wl(l, t, a, u), t.child);
  }
  function Xd(l, t, a, e, u, n) {
    return La(t), t.updateQueue = null, a = Ls(
      t,
      e,
      a,
      u
    ), Qs(l), e = ki(), l !== null && !Dl ? (Fi(l, t, n), Ft(l, t, n)) : (I && e && Oi(t), t.flags |= 1, wl(l, t, a, n), t.child);
  }
  function Zd(l, t, a, e, u) {
    if (La(t), t.stateNode === null) {
      var n = me, i = a.contextType;
      typeof i == "object" && i !== null && (n = Jl(i)), n = new a(e, n), t.memoizedState = n.state !== null && n.state !== void 0 ? n.state : null, n.updater = mc, t.stateNode = n, n._reactInternals = t, n = t.stateNode, n.props = e, n.state = t.memoizedState, n.refs = {}, Zi(t), i = a.contextType, n.context = typeof i == "object" && i !== null ? Jl(i) : me, n.state = t.memoizedState, i = a.getDerivedStateFromProps, typeof i == "function" && (oc(
        t,
        a,
        i,
        e
      ), n.state = t.memoizedState), typeof a.getDerivedStateFromProps == "function" || typeof n.getSnapshotBeforeUpdate == "function" || typeof n.UNSAFE_componentWillMount != "function" && typeof n.componentWillMount != "function" || (i = n.state, typeof n.componentWillMount == "function" && n.componentWillMount(), typeof n.UNSAFE_componentWillMount == "function" && n.UNSAFE_componentWillMount(), i !== n.state && mc.enqueueReplaceState(n, n.state, null), cu(t, e, n, u), iu(), n.state = t.memoizedState), typeof n.componentDidMount == "function" && (t.flags |= 4194308), e = !0;
    } else if (l === null) {
      n = t.stateNode;
      var c = t.memoizedProps, s = Wa(a, c);
      n.props = s;
      var y = n.context, z = a.contextType;
      i = me, typeof z == "object" && z !== null && (i = Jl(z));
      var T = a.getDerivedStateFromProps;
      z = typeof T == "function" || typeof n.getSnapshotBeforeUpdate == "function", c = t.pendingProps !== c, z || typeof n.UNSAFE_componentWillReceiveProps != "function" && typeof n.componentWillReceiveProps != "function" || (c || y !== i) && _d(
        t,
        n,
        e,
        i
      ), ha = !1;
      var r = t.memoizedState;
      n.state = r, cu(t, e, n, u), iu(), y = t.memoizedState, c || r !== y || ha ? (typeof T == "function" && (oc(
        t,
        a,
        T,
        e
      ), y = t.memoizedState), (s = ha || jd(
        t,
        a,
        s,
        e,
        r,
        y,
        i
      )) ? (z || typeof n.UNSAFE_componentWillMount != "function" && typeof n.componentWillMount != "function" || (typeof n.componentWillMount == "function" && n.componentWillMount(), typeof n.UNSAFE_componentWillMount == "function" && n.UNSAFE_componentWillMount()), typeof n.componentDidMount == "function" && (t.flags |= 4194308)) : (typeof n.componentDidMount == "function" && (t.flags |= 4194308), t.memoizedProps = e, t.memoizedState = y), n.props = e, n.state = y, n.context = i, e = s) : (typeof n.componentDidMount == "function" && (t.flags |= 4194308), e = !1);
    } else {
      n = t.stateNode, Qi(l, t), i = t.memoizedProps, z = Wa(a, i), n.props = z, T = t.pendingProps, r = n.context, y = a.contextType, s = me, typeof y == "object" && y !== null && (s = Jl(y)), c = a.getDerivedStateFromProps, (y = typeof c == "function" || typeof n.getSnapshotBeforeUpdate == "function") || typeof n.UNSAFE_componentWillReceiveProps != "function" && typeof n.componentWillReceiveProps != "function" || (i !== T || r !== s) && _d(
        t,
        n,
        e,
        s
      ), ha = !1, r = t.memoizedState, n.state = r, cu(t, e, n, u), iu();
      var S = t.memoizedState;
      i !== T || r !== S || ha || l !== null && l.dependencies !== null && tn(l.dependencies) ? (typeof c == "function" && (oc(
        t,
        a,
        c,
        e
      ), S = t.memoizedState), (z = ha || jd(
        t,
        a,
        z,
        e,
        r,
        S,
        s
      ) || l !== null && l.dependencies !== null && tn(l.dependencies)) ? (y || typeof n.UNSAFE_componentWillUpdate != "function" && typeof n.componentWillUpdate != "function" || (typeof n.componentWillUpdate == "function" && n.componentWillUpdate(e, S, s), typeof n.UNSAFE_componentWillUpdate == "function" && n.UNSAFE_componentWillUpdate(
        e,
        S,
        s
      )), typeof n.componentDidUpdate == "function" && (t.flags |= 4), typeof n.getSnapshotBeforeUpdate == "function" && (t.flags |= 1024)) : (typeof n.componentDidUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 4), typeof n.getSnapshotBeforeUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 1024), t.memoizedProps = e, t.memoizedState = S), n.props = e, n.state = S, n.context = s, e = z) : (typeof n.componentDidUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 4), typeof n.getSnapshotBeforeUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 1024), e = !1);
    }
    return n = e, zn(l, t), e = (t.flags & 128) !== 0, n || e ? (n = t.stateNode, a = e && typeof a.getDerivedStateFromError != "function" ? null : n.render(), t.flags |= 1, l !== null && e ? (t.child = wa(
      t,
      l.child,
      null,
      u
    ), t.child = wa(
      t,
      null,
      a,
      u
    )) : wl(l, t, a, u), t.memoizedState = n.state, l = t.child) : l = Ft(
      l,
      t,
      u
    ), l;
  }
  function Qd(l, t, a, e) {
    return Za(), t.flags |= 256, wl(l, t, a, e), t.child;
  }
  var rc = {
    dehydrated: null,
    treeContext: null,
    retryLane: 0,
    hydrationErrors: null
  };
  function gc(l) {
    return { baseLanes: l, cachePool: Ds() };
  }
  function Sc(l, t, a) {
    return l = l !== null ? l.childLanes & ~a : 0, t && (l |= vt), l;
  }
  function Ld(l, t, a) {
    var e = t.pendingProps, u = !1, n = (t.flags & 128) !== 0, i;
    if ((i = n) || (i = l !== null && l.memoizedState === null ? !1 : (_l.current & 2) !== 0), i && (u = !0, t.flags &= -129), i = (t.flags & 32) !== 0, t.flags &= -33, l === null) {
      if (I) {
        if (u ? ra(t) : ga(), (l = rl) ? (l = Io(
          l,
          jt
        ), l = l !== null && l.data !== "&" ? l : null, l !== null && (t.memoizedState = {
          dehydrated: l,
          treeContext: sa !== null ? { id: Yt, overflow: Bt } : null,
          retryLane: 536870912,
          hydrationErrors: null
        }, a = Es(l), a.return = t, t.child = a, Kl = t, rl = null)) : l = null, l === null) throw oa(t);
        return lf(l) ? t.lanes = 32 : t.lanes = 536870912, null;
      }
      var c = e.children;
      return e = e.fallback, u ? (ga(), u = t.mode, c = En(
        { mode: "hidden", children: c },
        u
      ), e = Xa(
        e,
        u,
        a,
        null
      ), c.return = t, e.return = t, c.sibling = e, t.child = c, e = t.child, e.memoizedState = gc(a), e.childLanes = Sc(
        l,
        i,
        a
      ), t.memoizedState = rc, mu(null, e)) : (ra(t), bc(t, c));
    }
    var s = l.memoizedState;
    if (s !== null && (c = s.dehydrated, c !== null)) {
      if (n)
        t.flags & 256 ? (ra(t), t.flags &= -257, t = pc(
          l,
          t,
          a
        )) : t.memoizedState !== null ? (ga(), t.child = l.child, t.flags |= 128, t = null) : (ga(), c = e.fallback, u = t.mode, e = En(
          { mode: "visible", children: e.children },
          u
        ), c = Xa(
          c,
          u,
          a,
          null
        ), c.flags |= 2, e.return = t, c.return = t, e.sibling = c, t.child = e, wa(
          t,
          l.child,
          null,
          a
        ), e = t.child, e.memoizedState = gc(a), e.childLanes = Sc(
          l,
          i,
          a
        ), t.memoizedState = rc, t = mu(null, e));
      else if (ra(t), lf(c)) {
        if (i = c.nextSibling && c.nextSibling.dataset, i) var y = i.dgst;
        i = y, e = Error(g(419)), e.stack = "", e.digest = i, lu({ value: e, source: null, stack: null }), t = pc(
          l,
          t,
          a
        );
      } else if (Dl || re(l, t, a, !1), i = (a & l.childLanes) !== 0, Dl || i) {
        if (i = vl, i !== null && (e = Mf(i, a), e !== 0 && e !== s.retryLane))
          throw s.retryLane = e, Ga(l, e), it(i, l, e), vc;
        Pc(c) || Dn(), t = pc(
          l,
          t,
          a
        );
      } else
        Pc(c) ? (t.flags |= 192, t.child = l.child, t = null) : (l = s.treeContext, rl = Nt(
          c.nextSibling
        ), Kl = t, I = !0, da = null, jt = !1, l !== null && xs(t, l), t = bc(
          t,
          e.children
        ), t.flags |= 4096);
      return t;
    }
    return u ? (ga(), c = e.fallback, u = t.mode, s = l.child, y = s.sibling, e = Kt(s, {
      mode: "hidden",
      children: e.children
    }), e.subtreeFlags = s.subtreeFlags & 65011712, y !== null ? c = Kt(
      y,
      c
    ) : (c = Xa(
      c,
      u,
      a,
      null
    ), c.flags |= 2), c.return = t, e.return = t, e.sibling = c, t.child = e, mu(null, e), e = t.child, c = l.child.memoizedState, c === null ? c = gc(a) : (u = c.cachePool, u !== null ? (s = Ml._currentValue, u = u.parent !== s ? { parent: s, pool: s } : u) : u = Ds(), c = {
      baseLanes: c.baseLanes | a,
      cachePool: u
    }), e.memoizedState = c, e.childLanes = Sc(
      l,
      i,
      a
    ), t.memoizedState = rc, mu(l.child, e)) : (ra(t), a = l.child, l = a.sibling, a = Kt(a, {
      mode: "visible",
      children: e.children
    }), a.return = t, a.sibling = null, l !== null && (i = t.deletions, i === null ? (t.deletions = [l], t.flags |= 16) : i.push(l)), t.child = a, t.memoizedState = null, a);
  }
  function bc(l, t) {
    return t = En(
      { mode: "visible", children: t },
      l.mode
    ), t.return = l, l.child = t;
  }
  function En(l, t) {
    return l = dt(22, l, null, t), l.lanes = 0, l;
  }
  function pc(l, t, a) {
    return wa(t, l.child, null, a), l = bc(
      t,
      t.pendingProps.children
    ), l.flags |= 2, t.memoizedState = null, l;
  }
  function Vd(l, t, a) {
    l.lanes |= t;
    var e = l.alternate;
    e !== null && (e.lanes |= t), Ri(l.return, t, a);
  }
  function zc(l, t, a, e, u, n) {
    var i = l.memoizedState;
    i === null ? l.memoizedState = {
      isBackwards: t,
      rendering: null,
      renderingStartTime: 0,
      last: e,
      tail: a,
      tailMode: u,
      treeForkCount: n
    } : (i.isBackwards = t, i.rendering = null, i.renderingStartTime = 0, i.last = e, i.tail = a, i.tailMode = u, i.treeForkCount = n);
  }
  function Kd(l, t, a) {
    var e = t.pendingProps, u = e.revealOrder, n = e.tail;
    e = e.children;
    var i = _l.current, c = (i & 2) !== 0;
    if (c ? (i = i & 1 | 2, t.flags |= 128) : i &= 1, j(_l, i), wl(l, t, e, a), e = I ? Pe : 0, !c && l !== null && (l.flags & 128) !== 0)
      l: for (l = t.child; l !== null; ) {
        if (l.tag === 13)
          l.memoizedState !== null && Vd(l, a, t);
        else if (l.tag === 19)
          Vd(l, a, t);
        else if (l.child !== null) {
          l.child.return = l, l = l.child;
          continue;
        }
        if (l === t) break l;
        for (; l.sibling === null; ) {
          if (l.return === null || l.return === t)
            break l;
          l = l.return;
        }
        l.sibling.return = l.return, l = l.sibling;
      }
    switch (u) {
      case "forwards":
        for (a = t.child, u = null; a !== null; )
          l = a.alternate, l !== null && dn(l) === null && (u = a), a = a.sibling;
        a = u, a === null ? (u = t.child, t.child = null) : (u = a.sibling, a.sibling = null), zc(
          t,
          !1,
          u,
          a,
          n,
          e
        );
        break;
      case "backwards":
      case "unstable_legacy-backwards":
        for (a = null, u = t.child, t.child = null; u !== null; ) {
          if (l = u.alternate, l !== null && dn(l) === null) {
            t.child = u;
            break;
          }
          l = u.sibling, u.sibling = a, a = u, u = l;
        }
        zc(
          t,
          !0,
          a,
          null,
          n,
          e
        );
        break;
      case "together":
        zc(
          t,
          !1,
          null,
          null,
          void 0,
          e
        );
        break;
      default:
        t.memoizedState = null;
    }
    return t.child;
  }
  function Ft(l, t, a) {
    if (l !== null && (t.dependencies = l.dependencies), pa |= t.lanes, (a & t.childLanes) === 0)
      if (l !== null) {
        if (re(
          l,
          t,
          a,
          !1
        ), (a & t.childLanes) === 0)
          return null;
      } else return null;
    if (l !== null && t.child !== l.child)
      throw Error(g(153));
    if (t.child !== null) {
      for (l = t.child, a = Kt(l, l.pendingProps), t.child = a, a.return = t; l.sibling !== null; )
        l = l.sibling, a = a.sibling = Kt(l, l.pendingProps), a.return = t;
      a.sibling = null;
    }
    return t.child;
  }
  function Ec(l, t) {
    return (l.lanes & t) !== 0 ? !0 : (l = l.dependencies, !!(l !== null && tn(l)));
  }
  function Ph(l, t, a) {
    switch (t.tag) {
      case 3:
        Rl(t, t.stateNode.containerInfo), ma(t, Ml, l.memoizedState.cache), Za();
        break;
      case 27:
      case 5:
        qt(t);
        break;
      case 4:
        Rl(t, t.stateNode.containerInfo);
        break;
      case 10:
        ma(
          t,
          t.type,
          t.memoizedProps.value
        );
        break;
      case 31:
        if (t.memoizedState !== null)
          return t.flags |= 128, wi(t), null;
        break;
      case 13:
        var e = t.memoizedState;
        if (e !== null)
          return e.dehydrated !== null ? (ra(t), t.flags |= 128, null) : (a & t.child.childLanes) !== 0 ? Ld(l, t, a) : (ra(t), l = Ft(
            l,
            t,
            a
          ), l !== null ? l.sibling : null);
        ra(t);
        break;
      case 19:
        var u = (l.flags & 128) !== 0;
        if (e = (a & t.childLanes) !== 0, e || (re(
          l,
          t,
          a,
          !1
        ), e = (a & t.childLanes) !== 0), u) {
          if (e)
            return Kd(
              l,
              t,
              a
            );
          t.flags |= 128;
        }
        if (u = t.memoizedState, u !== null && (u.rendering = null, u.tail = null, u.lastEffect = null), j(_l, _l.current), e) break;
        return null;
      case 22:
        return t.lanes = 0, Yd(
          l,
          t,
          a,
          t.pendingProps
        );
      case 24:
        ma(t, Ml, l.memoizedState.cache);
    }
    return Ft(l, t, a);
  }
  function Jd(l, t, a) {
    if (l !== null)
      if (l.memoizedProps !== t.pendingProps)
        Dl = !0;
      else {
        if (!Ec(l, a) && (t.flags & 128) === 0)
          return Dl = !1, Ph(
            l,
            t,
            a
          );
        Dl = (l.flags & 131072) !== 0;
      }
    else
      Dl = !1, I && (t.flags & 1048576) !== 0 && Ts(t, Pe, t.index);
    switch (t.lanes = 0, t.tag) {
      case 16:
        l: {
          var e = t.pendingProps;
          if (l = Ka(t.elementType), t.type = l, typeof l == "function")
            _i(l) ? (e = Wa(l, e), t.tag = 1, t = Zd(
              null,
              t,
              l,
              e,
              a
            )) : (t.tag = 0, t = yc(
              null,
              t,
              l,
              e,
              a
            ));
          else {
            if (l != null) {
              var u = l.$$typeof;
              if (u === Ql) {
                t.tag = 11, t = Cd(
                  null,
                  t,
                  l,
                  e,
                  a
                );
                break l;
              } else if (u === w) {
                t.tag = 14, t = Rd(
                  null,
                  t,
                  l,
                  e,
                  a
                );
                break l;
              }
            }
            throw t = St(l) || l, Error(g(306, t, ""));
          }
        }
        return t;
      case 0:
        return yc(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 1:
        return e = t.type, u = Wa(
          e,
          t.pendingProps
        ), Zd(
          l,
          t,
          e,
          u,
          a
        );
      case 3:
        l: {
          if (Rl(
            t,
            t.stateNode.containerInfo
          ), l === null) throw Error(g(387));
          e = t.pendingProps;
          var n = t.memoizedState;
          u = n.element, Qi(l, t), cu(t, e, null, a);
          var i = t.memoizedState;
          if (e = i.cache, ma(t, Ml, e), e !== n.cache && qi(
            t,
            [Ml],
            a,
            !0
          ), iu(), e = i.element, n.isDehydrated)
            if (n = {
              element: e,
              isDehydrated: !1,
              cache: i.cache
            }, t.updateQueue.baseState = n, t.memoizedState = n, t.flags & 256) {
              t = Qd(
                l,
                t,
                e,
                a
              );
              break l;
            } else if (e !== u) {
              u = At(
                Error(g(424)),
                t
              ), lu(u), t = Qd(
                l,
                t,
                e,
                a
              );
              break l;
            } else
              for (l = t.stateNode.containerInfo, l.nodeType === 9 ? l = l.body : l = l.nodeName === "HTML" ? l.ownerDocument.body : l, rl = Nt(l.firstChild), Kl = t, I = !0, da = null, jt = !0, a = Ys(
                t,
                null,
                e,
                a
              ), t.child = a; a; )
                a.flags = a.flags & -3 | 4096, a = a.sibling;
          else {
            if (Za(), e === u) {
              t = Ft(
                l,
                t,
                a
              );
              break l;
            }
            wl(l, t, e, a);
          }
          t = t.child;
        }
        return t;
      case 26:
        return zn(l, t), l === null ? (a = um(
          t.type,
          null,
          t.pendingProps,
          null
        )) ? t.memoizedState = a : I || (a = t.type, l = t.pendingProps, e = Bn(
          J.current
        ).createElement(a), e[Vl] = t, e[lt] = l, $l(e, a, l), Gl(e), t.stateNode = e) : t.memoizedState = um(
          t.type,
          l.memoizedProps,
          t.pendingProps,
          l.memoizedState
        ), null;
      case 27:
        return qt(t), l === null && I && (e = t.stateNode = tm(
          t.type,
          t.pendingProps,
          J.current
        ), Kl = t, jt = !0, u = rl, xa(t.type) ? (tf = u, rl = Nt(e.firstChild)) : rl = u), wl(
          l,
          t,
          t.pendingProps.children,
          a
        ), zn(l, t), l === null && (t.flags |= 4194304), t.child;
      case 5:
        return l === null && I && ((u = e = rl) && (e = Mv(
          e,
          t.type,
          t.pendingProps,
          jt
        ), e !== null ? (t.stateNode = e, Kl = t, rl = Nt(e.firstChild), jt = !1, u = !0) : u = !1), u || oa(t)), qt(t), u = t.type, n = t.pendingProps, i = l !== null ? l.memoizedProps : null, e = n.children, kc(u, n) ? e = null : i !== null && kc(u, i) && (t.flags |= 32), t.memoizedState !== null && (u = Wi(
          l,
          t,
          Vh,
          null,
          null,
          a
        ), _u._currentValue = u), zn(l, t), wl(l, t, e, a), t.child;
      case 6:
        return l === null && I && ((l = a = rl) && (a = Ov(
          a,
          t.pendingProps,
          jt
        ), a !== null ? (t.stateNode = a, Kl = t, rl = null, l = !0) : l = !1), l || oa(t)), null;
      case 13:
        return Ld(l, t, a);
      case 4:
        return Rl(
          t,
          t.stateNode.containerInfo
        ), e = t.pendingProps, l === null ? t.child = wa(
          t,
          null,
          e,
          a
        ) : wl(l, t, e, a), t.child;
      case 11:
        return Cd(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 7:
        return wl(
          l,
          t,
          t.pendingProps,
          a
        ), t.child;
      case 8:
        return wl(
          l,
          t,
          t.pendingProps.children,
          a
        ), t.child;
      case 12:
        return wl(
          l,
          t,
          t.pendingProps.children,
          a
        ), t.child;
      case 10:
        return e = t.pendingProps, ma(t, t.type, e.value), wl(l, t, e.children, a), t.child;
      case 9:
        return u = t.type._context, e = t.pendingProps.children, La(t), u = Jl(u), e = e(u), t.flags |= 1, wl(l, t, e, a), t.child;
      case 14:
        return Rd(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 15:
        return qd(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 19:
        return Kd(l, t, a);
      case 31:
        return Ih(l, t, a);
      case 22:
        return Yd(
          l,
          t,
          a,
          t.pendingProps
        );
      case 24:
        return La(t), e = Jl(Ml), l === null ? (u = Gi(), u === null && (u = vl, n = Yi(), u.pooledCache = n, n.refCount++, n !== null && (u.pooledCacheLanes |= a), u = n), t.memoizedState = { parent: e, cache: u }, Zi(t), ma(t, Ml, u)) : ((l.lanes & a) !== 0 && (Qi(l, t), cu(t, null, null, a), iu()), u = l.memoizedState, n = t.memoizedState, u.parent !== e ? (u = { parent: e, cache: e }, t.memoizedState = u, t.lanes === 0 && (t.memoizedState = t.updateQueue.baseState = u), ma(t, Ml, e)) : (e = n.cache, ma(t, Ml, e), e !== u.cache && qi(
          t,
          [Ml],
          a,
          !0
        ))), wl(
          l,
          t,
          t.pendingProps.children,
          a
        ), t.child;
      case 29:
        throw t.pendingProps;
    }
    throw Error(g(156, t.tag));
  }
  function It(l) {
    l.flags |= 4;
  }
  function Ac(l, t, a, e, u) {
    if ((t = (l.mode & 32) !== 0) && (t = !1), t) {
      if (l.flags |= 16777216, (u & 335544128) === u)
        if (l.stateNode.complete) l.flags |= 8192;
        else if (po()) l.flags |= 8192;
        else
          throw Ja = nn, Xi;
    } else l.flags &= -16777217;
  }
  function wd(l, t) {
    if (t.type !== "stylesheet" || (t.state.loading & 4) !== 0)
      l.flags &= -16777217;
    else if (l.flags |= 16777216, !sm(t))
      if (po()) l.flags |= 8192;
      else
        throw Ja = nn, Xi;
  }
  function An(l, t) {
    t !== null && (l.flags |= 4), l.flags & 16384 && (t = l.tag !== 22 ? jf() : 536870912, l.lanes |= t, Ne |= t);
  }
  function hu(l, t) {
    if (!I)
      switch (l.tailMode) {
        case "hidden":
          t = l.tail;
          for (var a = null; t !== null; )
            t.alternate !== null && (a = t), t = t.sibling;
          a === null ? l.tail = null : a.sibling = null;
          break;
        case "collapsed":
          a = l.tail;
          for (var e = null; a !== null; )
            a.alternate !== null && (e = a), a = a.sibling;
          e === null ? t || l.tail === null ? l.tail = null : l.tail.sibling = null : e.sibling = null;
      }
  }
  function gl(l) {
    var t = l.alternate !== null && l.alternate.child === l.child, a = 0, e = 0;
    if (t)
      for (var u = l.child; u !== null; )
        a |= u.lanes | u.childLanes, e |= u.subtreeFlags & 65011712, e |= u.flags & 65011712, u.return = l, u = u.sibling;
    else
      for (u = l.child; u !== null; )
        a |= u.lanes | u.childLanes, e |= u.subtreeFlags, e |= u.flags, u.return = l, u = u.sibling;
    return l.subtreeFlags |= e, l.childLanes = a, t;
  }
  function lv(l, t, a) {
    var e = t.pendingProps;
    switch (Di(t), t.tag) {
      case 16:
      case 15:
      case 0:
      case 11:
      case 7:
      case 8:
      case 12:
      case 9:
      case 14:
        return gl(t), null;
      case 1:
        return gl(t), null;
      case 3:
        return a = t.stateNode, e = null, l !== null && (e = l.memoizedState.cache), t.memoizedState.cache !== e && (t.flags |= 2048), $t(Ml), fl(), a.pendingContext && (a.context = a.pendingContext, a.pendingContext = null), (l === null || l.child === null) && (ye(t) ? It(t) : l === null || l.memoizedState.isDehydrated && (t.flags & 256) === 0 || (t.flags |= 1024, Hi())), gl(t), null;
      case 26:
        var u = t.type, n = t.memoizedState;
        return l === null ? (It(t), n !== null ? (gl(t), wd(t, n)) : (gl(t), Ac(
          t,
          u,
          null,
          e,
          a
        ))) : n ? n !== l.memoizedState ? (It(t), gl(t), wd(t, n)) : (gl(t), t.flags &= -16777217) : (l = l.memoizedProps, l !== e && It(t), gl(t), Ac(
          t,
          u,
          l,
          e,
          a
        )), null;
      case 27:
        if (bt(t), a = J.current, u = t.type, l !== null && t.stateNode != null)
          l.memoizedProps !== e && It(t);
        else {
          if (!e) {
            if (t.stateNode === null)
              throw Error(g(166));
            return gl(t), null;
          }
          l = O.current, ye(t) ? js(t) : (l = tm(u, e, a), t.stateNode = l, It(t));
        }
        return gl(t), null;
      case 5:
        if (bt(t), u = t.type, l !== null && t.stateNode != null)
          l.memoizedProps !== e && It(t);
        else {
          if (!e) {
            if (t.stateNode === null)
              throw Error(g(166));
            return gl(t), null;
          }
          if (n = O.current, ye(t))
            js(t);
          else {
            var i = Bn(
              J.current
            );
            switch (n) {
              case 1:
                n = i.createElementNS(
                  "http://www.w3.org/2000/svg",
                  u
                );
                break;
              case 2:
                n = i.createElementNS(
                  "http://www.w3.org/1998/Math/MathML",
                  u
                );
                break;
              default:
                switch (u) {
                  case "svg":
                    n = i.createElementNS(
                      "http://www.w3.org/2000/svg",
                      u
                    );
                    break;
                  case "math":
                    n = i.createElementNS(
                      "http://www.w3.org/1998/Math/MathML",
                      u
                    );
                    break;
                  case "script":
                    n = i.createElement("div"), n.innerHTML = "<script><\/script>", n = n.removeChild(
                      n.firstChild
                    );
                    break;
                  case "select":
                    n = typeof e.is == "string" ? i.createElement("select", {
                      is: e.is
                    }) : i.createElement("select"), e.multiple ? n.multiple = !0 : e.size && (n.size = e.size);
                    break;
                  default:
                    n = typeof e.is == "string" ? i.createElement(u, { is: e.is }) : i.createElement(u);
                }
            }
            n[Vl] = t, n[lt] = e;
            l: for (i = t.child; i !== null; ) {
              if (i.tag === 5 || i.tag === 6)
                n.appendChild(i.stateNode);
              else if (i.tag !== 4 && i.tag !== 27 && i.child !== null) {
                i.child.return = i, i = i.child;
                continue;
              }
              if (i === t) break l;
              for (; i.sibling === null; ) {
                if (i.return === null || i.return === t)
                  break l;
                i = i.return;
              }
              i.sibling.return = i.return, i = i.sibling;
            }
            t.stateNode = n;
            l: switch ($l(n, u, e), u) {
              case "button":
              case "input":
              case "select":
              case "textarea":
                e = !!e.autoFocus;
                break l;
              case "img":
                e = !0;
                break l;
              default:
                e = !1;
            }
            e && It(t);
          }
        }
        return gl(t), Ac(
          t,
          t.type,
          l === null ? null : l.memoizedProps,
          t.pendingProps,
          a
        ), null;
      case 6:
        if (l && t.stateNode != null)
          l.memoizedProps !== e && It(t);
        else {
          if (typeof e != "string" && t.stateNode === null)
            throw Error(g(166));
          if (l = J.current, ye(t)) {
            if (l = t.stateNode, a = t.memoizedProps, e = null, u = Kl, u !== null)
              switch (u.tag) {
                case 27:
                case 5:
                  e = u.memoizedProps;
              }
            l[Vl] = t, l = !!(l.nodeValue === a || e !== null && e.suppressHydrationWarning === !0 || Vo(l.nodeValue, a)), l || oa(t, !0);
          } else
            l = Bn(l).createTextNode(
              e
            ), l[Vl] = t, t.stateNode = l;
        }
        return gl(t), null;
      case 31:
        if (a = t.memoizedState, l === null || l.memoizedState !== null) {
          if (e = ye(t), a !== null) {
            if (l === null) {
              if (!e) throw Error(g(318));
              if (l = t.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(557));
              l[Vl] = t;
            } else
              Za(), (t.flags & 128) === 0 && (t.memoizedState = null), t.flags |= 4;
            gl(t), l = !1;
          } else
            a = Hi(), l !== null && l.memoizedState !== null && (l.memoizedState.hydrationErrors = a), l = !0;
          if (!l)
            return t.flags & 256 ? (mt(t), t) : (mt(t), null);
          if ((t.flags & 128) !== 0)
            throw Error(g(558));
        }
        return gl(t), null;
      case 13:
        if (e = t.memoizedState, l === null || l.memoizedState !== null && l.memoizedState.dehydrated !== null) {
          if (u = ye(t), e !== null && e.dehydrated !== null) {
            if (l === null) {
              if (!u) throw Error(g(318));
              if (u = t.memoizedState, u = u !== null ? u.dehydrated : null, !u) throw Error(g(317));
              u[Vl] = t;
            } else
              Za(), (t.flags & 128) === 0 && (t.memoizedState = null), t.flags |= 4;
            gl(t), u = !1;
          } else
            u = Hi(), l !== null && l.memoizedState !== null && (l.memoizedState.hydrationErrors = u), u = !0;
          if (!u)
            return t.flags & 256 ? (mt(t), t) : (mt(t), null);
        }
        return mt(t), (t.flags & 128) !== 0 ? (t.lanes = a, t) : (a = e !== null, l = l !== null && l.memoizedState !== null, a && (e = t.child, u = null, e.alternate !== null && e.alternate.memoizedState !== null && e.alternate.memoizedState.cachePool !== null && (u = e.alternate.memoizedState.cachePool.pool), n = null, e.memoizedState !== null && e.memoizedState.cachePool !== null && (n = e.memoizedState.cachePool.pool), n !== u && (e.flags |= 2048)), a !== l && a && (t.child.flags |= 8192), An(t, t.updateQueue), gl(t), null);
      case 4:
        return fl(), l === null && Kc(t.stateNode.containerInfo), gl(t), null;
      case 10:
        return $t(t.type), gl(t), null;
      case 19:
        if (E(_l), e = t.memoizedState, e === null) return gl(t), null;
        if (u = (t.flags & 128) !== 0, n = e.rendering, n === null)
          if (u) hu(e, !1);
          else {
            if (xl !== 0 || l !== null && (l.flags & 128) !== 0)
              for (l = t.child; l !== null; ) {
                if (n = dn(l), n !== null) {
                  for (t.flags |= 128, hu(e, !1), l = n.updateQueue, t.updateQueue = l, An(t, l), t.subtreeFlags = 0, l = a, a = t.child; a !== null; )
                    zs(a, l), a = a.sibling;
                  return j(
                    _l,
                    _l.current & 1 | 2
                  ), I && Jt(t, e.treeForkCount), t.child;
                }
                l = l.sibling;
              }
            e.tail !== null && Fl() > Nn && (t.flags |= 128, u = !0, hu(e, !1), t.lanes = 4194304);
          }
        else {
          if (!u)
            if (l = dn(n), l !== null) {
              if (t.flags |= 128, u = !0, l = l.updateQueue, t.updateQueue = l, An(t, l), hu(e, !0), e.tail === null && e.tailMode === "hidden" && !n.alternate && !I)
                return gl(t), null;
            } else
              2 * Fl() - e.renderingStartTime > Nn && a !== 536870912 && (t.flags |= 128, u = !0, hu(e, !1), t.lanes = 4194304);
          e.isBackwards ? (n.sibling = t.child, t.child = n) : (l = e.last, l !== null ? l.sibling = n : t.child = n, e.last = n);
        }
        return e.tail !== null ? (l = e.tail, e.rendering = l, e.tail = l.sibling, e.renderingStartTime = Fl(), l.sibling = null, a = _l.current, j(
          _l,
          u ? a & 1 | 2 : a & 1
        ), I && Jt(t, e.treeForkCount), l) : (gl(t), null);
      case 22:
      case 23:
        return mt(t), Ji(), e = t.memoizedState !== null, l !== null ? l.memoizedState !== null !== e && (t.flags |= 8192) : e && (t.flags |= 8192), e ? (a & 536870912) !== 0 && (t.flags & 128) === 0 && (gl(t), t.subtreeFlags & 6 && (t.flags |= 8192)) : gl(t), a = t.updateQueue, a !== null && An(t, a.retryQueue), a = null, l !== null && l.memoizedState !== null && l.memoizedState.cachePool !== null && (a = l.memoizedState.cachePool.pool), e = null, t.memoizedState !== null && t.memoizedState.cachePool !== null && (e = t.memoizedState.cachePool.pool), e !== a && (t.flags |= 2048), l !== null && E(Va), null;
      case 24:
        return a = null, l !== null && (a = l.memoizedState.cache), t.memoizedState.cache !== a && (t.flags |= 2048), $t(Ml), gl(t), null;
      case 25:
        return null;
      case 30:
        return null;
    }
    throw Error(g(156, t.tag));
  }
  function tv(l, t) {
    switch (Di(t), t.tag) {
      case 1:
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 3:
        return $t(Ml), fl(), l = t.flags, (l & 65536) !== 0 && (l & 128) === 0 ? (t.flags = l & -65537 | 128, t) : null;
      case 26:
      case 27:
      case 5:
        return bt(t), null;
      case 31:
        if (t.memoizedState !== null) {
          if (mt(t), t.alternate === null)
            throw Error(g(340));
          Za();
        }
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 13:
        if (mt(t), l = t.memoizedState, l !== null && l.dehydrated !== null) {
          if (t.alternate === null)
            throw Error(g(340));
          Za();
        }
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 19:
        return E(_l), null;
      case 4:
        return fl(), null;
      case 10:
        return $t(t.type), null;
      case 22:
      case 23:
        return mt(t), Ji(), l !== null && E(Va), l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 24:
        return $t(Ml), null;
      case 25:
        return null;
      default:
        return null;
    }
  }
  function $d(l, t) {
    switch (Di(t), t.tag) {
      case 3:
        $t(Ml), fl();
        break;
      case 26:
      case 27:
      case 5:
        bt(t);
        break;
      case 4:
        fl();
        break;
      case 31:
        t.memoizedState !== null && mt(t);
        break;
      case 13:
        mt(t);
        break;
      case 19:
        E(_l);
        break;
      case 10:
        $t(t.type);
        break;
      case 22:
      case 23:
        mt(t), Ji(), l !== null && E(Va);
        break;
      case 24:
        $t(Ml);
    }
  }
  function vu(l, t) {
    try {
      var a = t.updateQueue, e = a !== null ? a.lastEffect : null;
      if (e !== null) {
        var u = e.next;
        a = u;
        do {
          if ((a.tag & l) === l) {
            e = void 0;
            var n = a.create, i = a.inst;
            e = n(), i.destroy = e;
          }
          a = a.next;
        } while (a !== u);
      }
    } catch (c) {
      dl(t, t.return, c);
    }
  }
  function Sa(l, t, a) {
    try {
      var e = t.updateQueue, u = e !== null ? e.lastEffect : null;
      if (u !== null) {
        var n = u.next;
        e = n;
        do {
          if ((e.tag & l) === l) {
            var i = e.inst, c = i.destroy;
            if (c !== void 0) {
              i.destroy = void 0, u = t;
              var s = a, y = c;
              try {
                y();
              } catch (z) {
                dl(
                  u,
                  s,
                  z
                );
              }
            }
          }
          e = e.next;
        } while (e !== n);
      }
    } catch (z) {
      dl(t, t.return, z);
    }
  }
  function Wd(l) {
    var t = l.updateQueue;
    if (t !== null) {
      var a = l.stateNode;
      try {
        Gs(t, a);
      } catch (e) {
        dl(l, l.return, e);
      }
    }
  }
  function kd(l, t, a) {
    a.props = Wa(
      l.type,
      l.memoizedProps
    ), a.state = l.memoizedState;
    try {
      a.componentWillUnmount();
    } catch (e) {
      dl(l, t, e);
    }
  }
  function yu(l, t) {
    try {
      var a = l.ref;
      if (a !== null) {
        switch (l.tag) {
          case 26:
          case 27:
          case 5:
            var e = l.stateNode;
            break;
          case 30:
            e = l.stateNode;
            break;
          default:
            e = l.stateNode;
        }
        typeof a == "function" ? l.refCleanup = a(e) : a.current = e;
      }
    } catch (u) {
      dl(l, t, u);
    }
  }
  function Gt(l, t) {
    var a = l.ref, e = l.refCleanup;
    if (a !== null)
      if (typeof e == "function")
        try {
          e();
        } catch (u) {
          dl(l, t, u);
        } finally {
          l.refCleanup = null, l = l.alternate, l != null && (l.refCleanup = null);
        }
      else if (typeof a == "function")
        try {
          a(null);
        } catch (u) {
          dl(l, t, u);
        }
      else a.current = null;
  }
  function Fd(l) {
    var t = l.type, a = l.memoizedProps, e = l.stateNode;
    try {
      l: switch (t) {
        case "button":
        case "input":
        case "select":
        case "textarea":
          a.autoFocus && e.focus();
          break l;
        case "img":
          a.src ? e.src = a.src : a.srcSet && (e.srcset = a.srcSet);
      }
    } catch (u) {
      dl(l, l.return, u);
    }
  }
  function Tc(l, t, a) {
    try {
      var e = l.stateNode;
      Av(e, l.type, a, t), e[lt] = t;
    } catch (u) {
      dl(l, l.return, u);
    }
  }
  function Id(l) {
    return l.tag === 5 || l.tag === 3 || l.tag === 26 || l.tag === 27 && xa(l.type) || l.tag === 4;
  }
  function xc(l) {
    l: for (; ; ) {
      for (; l.sibling === null; ) {
        if (l.return === null || Id(l.return)) return null;
        l = l.return;
      }
      for (l.sibling.return = l.return, l = l.sibling; l.tag !== 5 && l.tag !== 6 && l.tag !== 18; ) {
        if (l.tag === 27 && xa(l.type) || l.flags & 2 || l.child === null || l.tag === 4) continue l;
        l.child.return = l, l = l.child;
      }
      if (!(l.flags & 2)) return l.stateNode;
    }
  }
  function jc(l, t, a) {
    var e = l.tag;
    if (e === 5 || e === 6)
      l = l.stateNode, t ? (a.nodeType === 9 ? a.body : a.nodeName === "HTML" ? a.ownerDocument.body : a).insertBefore(l, t) : (t = a.nodeType === 9 ? a.body : a.nodeName === "HTML" ? a.ownerDocument.body : a, t.appendChild(l), a = a._reactRootContainer, a != null || t.onclick !== null || (t.onclick = Lt));
    else if (e !== 4 && (e === 27 && xa(l.type) && (a = l.stateNode, t = null), l = l.child, l !== null))
      for (jc(l, t, a), l = l.sibling; l !== null; )
        jc(l, t, a), l = l.sibling;
  }
  function Tn(l, t, a) {
    var e = l.tag;
    if (e === 5 || e === 6)
      l = l.stateNode, t ? a.insertBefore(l, t) : a.appendChild(l);
    else if (e !== 4 && (e === 27 && xa(l.type) && (a = l.stateNode), l = l.child, l !== null))
      for (Tn(l, t, a), l = l.sibling; l !== null; )
        Tn(l, t, a), l = l.sibling;
  }
  function Pd(l) {
    var t = l.stateNode, a = l.memoizedProps;
    try {
      for (var e = l.type, u = t.attributes; u.length; )
        t.removeAttributeNode(u[0]);
      $l(t, e, a), t[Vl] = l, t[lt] = a;
    } catch (n) {
      dl(l, l.return, n);
    }
  }
  var Pt = !1, Ul = !1, _c = !1, lo = typeof WeakSet == "function" ? WeakSet : Set, Xl = null;
  function av(l, t) {
    if (l = l.containerInfo, $c = Kn, l = ms(l), pi(l)) {
      if ("selectionStart" in l)
        var a = {
          start: l.selectionStart,
          end: l.selectionEnd
        };
      else
        l: {
          a = (a = l.ownerDocument) && a.defaultView || window;
          var e = a.getSelection && a.getSelection();
          if (e && e.rangeCount !== 0) {
            a = e.anchorNode;
            var u = e.anchorOffset, n = e.focusNode;
            e = e.focusOffset;
            try {
              a.nodeType, n.nodeType;
            } catch {
              a = null;
              break l;
            }
            var i = 0, c = -1, s = -1, y = 0, z = 0, T = l, r = null;
            t: for (; ; ) {
              for (var S; T !== a || u !== 0 && T.nodeType !== 3 || (c = i + u), T !== n || e !== 0 && T.nodeType !== 3 || (s = i + e), T.nodeType === 3 && (i += T.nodeValue.length), (S = T.firstChild) !== null; )
                r = T, T = S;
              for (; ; ) {
                if (T === l) break t;
                if (r === a && ++y === u && (c = i), r === n && ++z === e && (s = i), (S = T.nextSibling) !== null) break;
                T = r, r = T.parentNode;
              }
              T = S;
            }
            a = c === -1 || s === -1 ? null : { start: c, end: s };
          } else a = null;
        }
      a = a || { start: 0, end: 0 };
    } else a = null;
    for (Wc = { focusedElem: l, selectionRange: a }, Kn = !1, Xl = t; Xl !== null; )
      if (t = Xl, l = t.child, (t.subtreeFlags & 1028) !== 0 && l !== null)
        l.return = t, Xl = l;
      else
        for (; Xl !== null; ) {
          switch (t = Xl, n = t.alternate, l = t.flags, t.tag) {
            case 0:
              if ((l & 4) !== 0 && (l = t.updateQueue, l = l !== null ? l.events : null, l !== null))
                for (a = 0; a < l.length; a++)
                  u = l[a], u.ref.impl = u.nextImpl;
              break;
            case 11:
            case 15:
              break;
            case 1:
              if ((l & 1024) !== 0 && n !== null) {
                l = void 0, a = t, u = n.memoizedProps, n = n.memoizedState, e = a.stateNode;
                try {
                  var D = Wa(
                    a.type,
                    u
                  );
                  l = e.getSnapshotBeforeUpdate(
                    D,
                    n
                  ), e.__reactInternalSnapshotBeforeUpdate = l;
                } catch (B) {
                  dl(
                    a,
                    a.return,
                    B
                  );
                }
              }
              break;
            case 3:
              if ((l & 1024) !== 0) {
                if (l = t.stateNode.containerInfo, a = l.nodeType, a === 9)
                  Ic(l);
                else if (a === 1)
                  switch (l.nodeName) {
                    case "HEAD":
                    case "HTML":
                    case "BODY":
                      Ic(l);
                      break;
                    default:
                      l.textContent = "";
                  }
              }
              break;
            case 5:
            case 26:
            case 27:
            case 6:
            case 4:
            case 17:
              break;
            default:
              if ((l & 1024) !== 0) throw Error(g(163));
          }
          if (l = t.sibling, l !== null) {
            l.return = t.return, Xl = l;
            break;
          }
          Xl = t.return;
        }
  }
  function to(l, t, a) {
    var e = a.flags;
    switch (a.tag) {
      case 0:
      case 11:
      case 15:
        ta(l, a), e & 4 && vu(5, a);
        break;
      case 1:
        if (ta(l, a), e & 4)
          if (l = a.stateNode, t === null)
            try {
              l.componentDidMount();
            } catch (i) {
              dl(a, a.return, i);
            }
          else {
            var u = Wa(
              a.type,
              t.memoizedProps
            );
            t = t.memoizedState;
            try {
              l.componentDidUpdate(
                u,
                t,
                l.__reactInternalSnapshotBeforeUpdate
              );
            } catch (i) {
              dl(
                a,
                a.return,
                i
              );
            }
          }
        e & 64 && Wd(a), e & 512 && yu(a, a.return);
        break;
      case 3:
        if (ta(l, a), e & 64 && (l = a.updateQueue, l !== null)) {
          if (t = null, a.child !== null)
            switch (a.child.tag) {
              case 27:
              case 5:
                t = a.child.stateNode;
                break;
              case 1:
                t = a.child.stateNode;
            }
          try {
            Gs(l, t);
          } catch (i) {
            dl(a, a.return, i);
          }
        }
        break;
      case 27:
        t === null && e & 4 && Pd(a);
      case 26:
      case 5:
        ta(l, a), t === null && e & 4 && Fd(a), e & 512 && yu(a, a.return);
        break;
      case 12:
        ta(l, a);
        break;
      case 31:
        ta(l, a), e & 4 && uo(l, a);
        break;
      case 13:
        ta(l, a), e & 4 && no(l, a), e & 64 && (l = a.memoizedState, l !== null && (l = l.dehydrated, l !== null && (a = ov.bind(
          null,
          a
        ), Dv(l, a))));
        break;
      case 22:
        if (e = a.memoizedState !== null || Pt, !e) {
          t = t !== null && t.memoizedState !== null || Ul, u = Pt;
          var n = Ul;
          Pt = e, (Ul = t) && !n ? aa(
            l,
            a,
            (a.subtreeFlags & 8772) !== 0
          ) : ta(l, a), Pt = u, Ul = n;
        }
        break;
      case 30:
        break;
      default:
        ta(l, a);
    }
  }
  function ao(l) {
    var t = l.alternate;
    t !== null && (l.alternate = null, ao(t)), l.child = null, l.deletions = null, l.sibling = null, l.tag === 5 && (t = l.stateNode, t !== null && ei(t)), l.stateNode = null, l.return = null, l.dependencies = null, l.memoizedProps = null, l.memoizedState = null, l.pendingProps = null, l.stateNode = null, l.updateQueue = null;
  }
  var pl = null, at = !1;
  function la(l, t, a) {
    for (a = a.child; a !== null; )
      eo(l, t, a), a = a.sibling;
  }
  function eo(l, t, a) {
    if (ct && typeof ct.onCommitFiberUnmount == "function")
      try {
        ct.onCommitFiberUnmount(Ge, a);
      } catch {
      }
    switch (a.tag) {
      case 26:
        Ul || Gt(a, t), la(
          l,
          t,
          a
        ), a.memoizedState ? a.memoizedState.count-- : a.stateNode && (a = a.stateNode, a.parentNode.removeChild(a));
        break;
      case 27:
        Ul || Gt(a, t);
        var e = pl, u = at;
        xa(a.type) && (pl = a.stateNode, at = !1), la(
          l,
          t,
          a
        ), Tu(a.stateNode), pl = e, at = u;
        break;
      case 5:
        Ul || Gt(a, t);
      case 6:
        if (e = pl, u = at, pl = null, la(
          l,
          t,
          a
        ), pl = e, at = u, pl !== null)
          if (at)
            try {
              (pl.nodeType === 9 ? pl.body : pl.nodeName === "HTML" ? pl.ownerDocument.body : pl).removeChild(a.stateNode);
            } catch (n) {
              dl(
                a,
                t,
                n
              );
            }
          else
            try {
              pl.removeChild(a.stateNode);
            } catch (n) {
              dl(
                a,
                t,
                n
              );
            }
        break;
      case 18:
        pl !== null && (at ? (l = pl, ko(
          l.nodeType === 9 ? l.body : l.nodeName === "HTML" ? l.ownerDocument.body : l,
          a.stateNode
        ), qe(l)) : ko(pl, a.stateNode));
        break;
      case 4:
        e = pl, u = at, pl = a.stateNode.containerInfo, at = !0, la(
          l,
          t,
          a
        ), pl = e, at = u;
        break;
      case 0:
      case 11:
      case 14:
      case 15:
        Sa(2, a, t), Ul || Sa(4, a, t), la(
          l,
          t,
          a
        );
        break;
      case 1:
        Ul || (Gt(a, t), e = a.stateNode, typeof e.componentWillUnmount == "function" && kd(
          a,
          t,
          e
        )), la(
          l,
          t,
          a
        );
        break;
      case 21:
        la(
          l,
          t,
          a
        );
        break;
      case 22:
        Ul = (e = Ul) || a.memoizedState !== null, la(
          l,
          t,
          a
        ), Ul = e;
        break;
      default:
        la(
          l,
          t,
          a
        );
    }
  }
  function uo(l, t) {
    if (t.memoizedState === null && (l = t.alternate, l !== null && (l = l.memoizedState, l !== null))) {
      l = l.dehydrated;
      try {
        qe(l);
      } catch (a) {
        dl(t, t.return, a);
      }
    }
  }
  function no(l, t) {
    if (t.memoizedState === null && (l = t.alternate, l !== null && (l = l.memoizedState, l !== null && (l = l.dehydrated, l !== null))))
      try {
        qe(l);
      } catch (a) {
        dl(t, t.return, a);
      }
  }
  function ev(l) {
    switch (l.tag) {
      case 31:
      case 13:
      case 19:
        var t = l.stateNode;
        return t === null && (t = l.stateNode = new lo()), t;
      case 22:
        return l = l.stateNode, t = l._retryCache, t === null && (t = l._retryCache = new lo()), t;
      default:
        throw Error(g(435, l.tag));
    }
  }
  function xn(l, t) {
    var a = ev(l);
    t.forEach(function(e) {
      if (!a.has(e)) {
        a.add(e);
        var u = mv.bind(null, l, e);
        e.then(u, u);
      }
    });
  }
  function et(l, t) {
    var a = t.deletions;
    if (a !== null)
      for (var e = 0; e < a.length; e++) {
        var u = a[e], n = l, i = t, c = i;
        l: for (; c !== null; ) {
          switch (c.tag) {
            case 27:
              if (xa(c.type)) {
                pl = c.stateNode, at = !1;
                break l;
              }
              break;
            case 5:
              pl = c.stateNode, at = !1;
              break l;
            case 3:
            case 4:
              pl = c.stateNode.containerInfo, at = !0;
              break l;
          }
          c = c.return;
        }
        if (pl === null) throw Error(g(160));
        eo(n, i, u), pl = null, at = !1, n = u.alternate, n !== null && (n.return = null), u.return = null;
      }
    if (t.subtreeFlags & 13886)
      for (t = t.child; t !== null; )
        io(t, l), t = t.sibling;
  }
  var Ut = null;
  function io(l, t) {
    var a = l.alternate, e = l.flags;
    switch (l.tag) {
      case 0:
      case 11:
      case 14:
      case 15:
        et(t, l), ut(l), e & 4 && (Sa(3, l, l.return), vu(3, l), Sa(5, l, l.return));
        break;
      case 1:
        et(t, l), ut(l), e & 512 && (Ul || a === null || Gt(a, a.return)), e & 64 && Pt && (l = l.updateQueue, l !== null && (e = l.callbacks, e !== null && (a = l.shared.hiddenCallbacks, l.shared.hiddenCallbacks = a === null ? e : a.concat(e))));
        break;
      case 26:
        var u = Ut;
        if (et(t, l), ut(l), e & 512 && (Ul || a === null || Gt(a, a.return)), e & 4) {
          var n = a !== null ? a.memoizedState : null;
          if (e = l.memoizedState, a === null)
            if (e === null)
              if (l.stateNode === null) {
                l: {
                  e = l.type, a = l.memoizedProps, u = u.ownerDocument || u;
                  t: switch (e) {
                    case "title":
                      n = u.getElementsByTagName("title")[0], (!n || n[Qe] || n[Vl] || n.namespaceURI === "http://www.w3.org/2000/svg" || n.hasAttribute("itemprop")) && (n = u.createElement(e), u.head.insertBefore(
                        n,
                        u.querySelector("head > title")
                      )), $l(n, e, a), n[Vl] = l, Gl(n), e = n;
                      break l;
                    case "link":
                      var i = cm(
                        "link",
                        "href",
                        u
                      ).get(e + (a.href || ""));
                      if (i) {
                        for (var c = 0; c < i.length; c++)
                          if (n = i[c], n.getAttribute("href") === (a.href == null || a.href === "" ? null : a.href) && n.getAttribute("rel") === (a.rel == null ? null : a.rel) && n.getAttribute("title") === (a.title == null ? null : a.title) && n.getAttribute("crossorigin") === (a.crossOrigin == null ? null : a.crossOrigin)) {
                            i.splice(c, 1);
                            break t;
                          }
                      }
                      n = u.createElement(e), $l(n, e, a), u.head.appendChild(n);
                      break;
                    case "meta":
                      if (i = cm(
                        "meta",
                        "content",
                        u
                      ).get(e + (a.content || ""))) {
                        for (c = 0; c < i.length; c++)
                          if (n = i[c], n.getAttribute("content") === (a.content == null ? null : "" + a.content) && n.getAttribute("name") === (a.name == null ? null : a.name) && n.getAttribute("property") === (a.property == null ? null : a.property) && n.getAttribute("http-equiv") === (a.httpEquiv == null ? null : a.httpEquiv) && n.getAttribute("charset") === (a.charSet == null ? null : a.charSet)) {
                            i.splice(c, 1);
                            break t;
                          }
                      }
                      n = u.createElement(e), $l(n, e, a), u.head.appendChild(n);
                      break;
                    default:
                      throw Error(g(468, e));
                  }
                  n[Vl] = l, Gl(n), e = n;
                }
                l.stateNode = e;
              } else
                fm(
                  u,
                  l.type,
                  l.stateNode
                );
            else
              l.stateNode = im(
                u,
                e,
                l.memoizedProps
              );
          else
            n !== e ? (n === null ? a.stateNode !== null && (a = a.stateNode, a.parentNode.removeChild(a)) : n.count--, e === null ? fm(
              u,
              l.type,
              l.stateNode
            ) : im(
              u,
              e,
              l.memoizedProps
            )) : e === null && l.stateNode !== null && Tc(
              l,
              l.memoizedProps,
              a.memoizedProps
            );
        }
        break;
      case 27:
        et(t, l), ut(l), e & 512 && (Ul || a === null || Gt(a, a.return)), a !== null && e & 4 && Tc(
          l,
          l.memoizedProps,
          a.memoizedProps
        );
        break;
      case 5:
        if (et(t, l), ut(l), e & 512 && (Ul || a === null || Gt(a, a.return)), l.flags & 32) {
          u = l.stateNode;
          try {
            ne(u, "");
          } catch (D) {
            dl(l, l.return, D);
          }
        }
        e & 4 && l.stateNode != null && (u = l.memoizedProps, Tc(
          l,
          u,
          a !== null ? a.memoizedProps : u
        )), e & 1024 && (_c = !0);
        break;
      case 6:
        if (et(t, l), ut(l), e & 4) {
          if (l.stateNode === null)
            throw Error(g(162));
          e = l.memoizedProps, a = l.stateNode;
          try {
            a.nodeValue = e;
          } catch (D) {
            dl(l, l.return, D);
          }
        }
        break;
      case 3:
        if (Zn = null, u = Ut, Ut = Gn(t.containerInfo), et(t, l), Ut = u, ut(l), e & 4 && a !== null && a.memoizedState.isDehydrated)
          try {
            qe(t.containerInfo);
          } catch (D) {
            dl(l, l.return, D);
          }
        _c && (_c = !1, co(l));
        break;
      case 4:
        e = Ut, Ut = Gn(
          l.stateNode.containerInfo
        ), et(t, l), ut(l), Ut = e;
        break;
      case 12:
        et(t, l), ut(l);
        break;
      case 31:
        et(t, l), ut(l), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, xn(l, e)));
        break;
      case 13:
        et(t, l), ut(l), l.child.flags & 8192 && l.memoizedState !== null != (a !== null && a.memoizedState !== null) && (_n = Fl()), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, xn(l, e)));
        break;
      case 22:
        u = l.memoizedState !== null;
        var s = a !== null && a.memoizedState !== null, y = Pt, z = Ul;
        if (Pt = y || u, Ul = z || s, et(t, l), Ul = z, Pt = y, ut(l), e & 8192)
          l: for (t = l.stateNode, t._visibility = u ? t._visibility & -2 : t._visibility | 1, u && (a === null || s || Pt || Ul || ka(l)), a = null, t = l; ; ) {
            if (t.tag === 5 || t.tag === 26) {
              if (a === null) {
                s = a = t;
                try {
                  if (n = s.stateNode, u)
                    i = n.style, typeof i.setProperty == "function" ? i.setProperty("display", "none", "important") : i.display = "none";
                  else {
                    c = s.stateNode;
                    var T = s.memoizedProps.style, r = T != null && T.hasOwnProperty("display") ? T.display : null;
                    c.style.display = r == null || typeof r == "boolean" ? "" : ("" + r).trim();
                  }
                } catch (D) {
                  dl(s, s.return, D);
                }
              }
            } else if (t.tag === 6) {
              if (a === null) {
                s = t;
                try {
                  s.stateNode.nodeValue = u ? "" : s.memoizedProps;
                } catch (D) {
                  dl(s, s.return, D);
                }
              }
            } else if (t.tag === 18) {
              if (a === null) {
                s = t;
                try {
                  var S = s.stateNode;
                  u ? Fo(S, !0) : Fo(s.stateNode, !1);
                } catch (D) {
                  dl(s, s.return, D);
                }
              }
            } else if ((t.tag !== 22 && t.tag !== 23 || t.memoizedState === null || t === l) && t.child !== null) {
              t.child.return = t, t = t.child;
              continue;
            }
            if (t === l) break l;
            for (; t.sibling === null; ) {
              if (t.return === null || t.return === l) break l;
              a === t && (a = null), t = t.return;
            }
            a === t && (a = null), t.sibling.return = t.return, t = t.sibling;
          }
        e & 4 && (e = l.updateQueue, e !== null && (a = e.retryQueue, a !== null && (e.retryQueue = null, xn(l, a))));
        break;
      case 19:
        et(t, l), ut(l), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, xn(l, e)));
        break;
      case 30:
        break;
      case 21:
        break;
      default:
        et(t, l), ut(l);
    }
  }
  function ut(l) {
    var t = l.flags;
    if (t & 2) {
      try {
        for (var a, e = l.return; e !== null; ) {
          if (Id(e)) {
            a = e;
            break;
          }
          e = e.return;
        }
        if (a == null) throw Error(g(160));
        switch (a.tag) {
          case 27:
            var u = a.stateNode, n = xc(l);
            Tn(l, n, u);
            break;
          case 5:
            var i = a.stateNode;
            a.flags & 32 && (ne(i, ""), a.flags &= -33);
            var c = xc(l);
            Tn(l, c, i);
            break;
          case 3:
          case 4:
            var s = a.stateNode.containerInfo, y = xc(l);
            jc(
              l,
              y,
              s
            );
            break;
          default:
            throw Error(g(161));
        }
      } catch (z) {
        dl(l, l.return, z);
      }
      l.flags &= -3;
    }
    t & 4096 && (l.flags &= -4097);
  }
  function co(l) {
    if (l.subtreeFlags & 1024)
      for (l = l.child; l !== null; ) {
        var t = l;
        co(t), t.tag === 5 && t.flags & 1024 && t.stateNode.reset(), l = l.sibling;
      }
  }
  function ta(l, t) {
    if (t.subtreeFlags & 8772)
      for (t = t.child; t !== null; )
        to(l, t.alternate, t), t = t.sibling;
  }
  function ka(l) {
    for (l = l.child; l !== null; ) {
      var t = l;
      switch (t.tag) {
        case 0:
        case 11:
        case 14:
        case 15:
          Sa(4, t, t.return), ka(t);
          break;
        case 1:
          Gt(t, t.return);
          var a = t.stateNode;
          typeof a.componentWillUnmount == "function" && kd(
            t,
            t.return,
            a
          ), ka(t);
          break;
        case 27:
          Tu(t.stateNode);
        case 26:
        case 5:
          Gt(t, t.return), ka(t);
          break;
        case 22:
          t.memoizedState === null && ka(t);
          break;
        case 30:
          ka(t);
          break;
        default:
          ka(t);
      }
      l = l.sibling;
    }
  }
  function aa(l, t, a) {
    for (a = a && (t.subtreeFlags & 8772) !== 0, t = t.child; t !== null; ) {
      var e = t.alternate, u = l, n = t, i = n.flags;
      switch (n.tag) {
        case 0:
        case 11:
        case 15:
          aa(
            u,
            n,
            a
          ), vu(4, n);
          break;
        case 1:
          if (aa(
            u,
            n,
            a
          ), e = n, u = e.stateNode, typeof u.componentDidMount == "function")
            try {
              u.componentDidMount();
            } catch (y) {
              dl(e, e.return, y);
            }
          if (e = n, u = e.updateQueue, u !== null) {
            var c = e.stateNode;
            try {
              var s = u.shared.hiddenCallbacks;
              if (s !== null)
                for (u.shared.hiddenCallbacks = null, u = 0; u < s.length; u++)
                  Bs(s[u], c);
            } catch (y) {
              dl(e, e.return, y);
            }
          }
          a && i & 64 && Wd(n), yu(n, n.return);
          break;
        case 27:
          Pd(n);
        case 26:
        case 5:
          aa(
            u,
            n,
            a
          ), a && e === null && i & 4 && Fd(n), yu(n, n.return);
          break;
        case 12:
          aa(
            u,
            n,
            a
          );
          break;
        case 31:
          aa(
            u,
            n,
            a
          ), a && i & 4 && uo(u, n);
          break;
        case 13:
          aa(
            u,
            n,
            a
          ), a && i & 4 && no(u, n);
          break;
        case 22:
          n.memoizedState === null && aa(
            u,
            n,
            a
          ), yu(n, n.return);
          break;
        case 30:
          break;
        default:
          aa(
            u,
            n,
            a
          );
      }
      t = t.sibling;
    }
  }
  function Nc(l, t) {
    var a = null;
    l !== null && l.memoizedState !== null && l.memoizedState.cachePool !== null && (a = l.memoizedState.cachePool.pool), l = null, t.memoizedState !== null && t.memoizedState.cachePool !== null && (l = t.memoizedState.cachePool.pool), l !== a && (l != null && l.refCount++, a != null && tu(a));
  }
  function Mc(l, t) {
    l = null, t.alternate !== null && (l = t.alternate.memoizedState.cache), t = t.memoizedState.cache, t !== l && (t.refCount++, l != null && tu(l));
  }
  function Ht(l, t, a, e) {
    if (t.subtreeFlags & 10256)
      for (t = t.child; t !== null; )
        fo(
          l,
          t,
          a,
          e
        ), t = t.sibling;
  }
  function fo(l, t, a, e) {
    var u = t.flags;
    switch (t.tag) {
      case 0:
      case 11:
      case 15:
        Ht(
          l,
          t,
          a,
          e
        ), u & 2048 && vu(9, t);
        break;
      case 1:
        Ht(
          l,
          t,
          a,
          e
        );
        break;
      case 3:
        Ht(
          l,
          t,
          a,
          e
        ), u & 2048 && (l = null, t.alternate !== null && (l = t.alternate.memoizedState.cache), t = t.memoizedState.cache, t !== l && (t.refCount++, l != null && tu(l)));
        break;
      case 12:
        if (u & 2048) {
          Ht(
            l,
            t,
            a,
            e
          ), l = t.stateNode;
          try {
            var n = t.memoizedProps, i = n.id, c = n.onPostCommit;
            typeof c == "function" && c(
              i,
              t.alternate === null ? "mount" : "update",
              l.passiveEffectDuration,
              -0
            );
          } catch (s) {
            dl(t, t.return, s);
          }
        } else
          Ht(
            l,
            t,
            a,
            e
          );
        break;
      case 31:
        Ht(
          l,
          t,
          a,
          e
        );
        break;
      case 13:
        Ht(
          l,
          t,
          a,
          e
        );
        break;
      case 23:
        break;
      case 22:
        n = t.stateNode, i = t.alternate, t.memoizedState !== null ? n._visibility & 2 ? Ht(
          l,
          t,
          a,
          e
        ) : ru(l, t) : n._visibility & 2 ? Ht(
          l,
          t,
          a,
          e
        ) : (n._visibility |= 2, xe(
          l,
          t,
          a,
          e,
          (t.subtreeFlags & 10256) !== 0 || !1
        )), u & 2048 && Nc(i, t);
        break;
      case 24:
        Ht(
          l,
          t,
          a,
          e
        ), u & 2048 && Mc(t.alternate, t);
        break;
      default:
        Ht(
          l,
          t,
          a,
          e
        );
    }
  }
  function xe(l, t, a, e, u) {
    for (u = u && ((t.subtreeFlags & 10256) !== 0 || !1), t = t.child; t !== null; ) {
      var n = l, i = t, c = a, s = e, y = i.flags;
      switch (i.tag) {
        case 0:
        case 11:
        case 15:
          xe(
            n,
            i,
            c,
            s,
            u
          ), vu(8, i);
          break;
        case 23:
          break;
        case 22:
          var z = i.stateNode;
          i.memoizedState !== null ? z._visibility & 2 ? xe(
            n,
            i,
            c,
            s,
            u
          ) : ru(
            n,
            i
          ) : (z._visibility |= 2, xe(
            n,
            i,
            c,
            s,
            u
          )), u && y & 2048 && Nc(
            i.alternate,
            i
          );
          break;
        case 24:
          xe(
            n,
            i,
            c,
            s,
            u
          ), u && y & 2048 && Mc(i.alternate, i);
          break;
        default:
          xe(
            n,
            i,
            c,
            s,
            u
          );
      }
      t = t.sibling;
    }
  }
  function ru(l, t) {
    if (t.subtreeFlags & 10256)
      for (t = t.child; t !== null; ) {
        var a = l, e = t, u = e.flags;
        switch (e.tag) {
          case 22:
            ru(a, e), u & 2048 && Nc(
              e.alternate,
              e
            );
            break;
          case 24:
            ru(a, e), u & 2048 && Mc(e.alternate, e);
            break;
          default:
            ru(a, e);
        }
        t = t.sibling;
      }
  }
  var gu = 8192;
  function je(l, t, a) {
    if (l.subtreeFlags & gu)
      for (l = l.child; l !== null; )
        so(
          l,
          t,
          a
        ), l = l.sibling;
  }
  function so(l, t, a) {
    switch (l.tag) {
      case 26:
        je(
          l,
          t,
          a
        ), l.flags & gu && l.memoizedState !== null && Lv(
          a,
          Ut,
          l.memoizedState,
          l.memoizedProps
        );
        break;
      case 5:
        je(
          l,
          t,
          a
        );
        break;
      case 3:
      case 4:
        var e = Ut;
        Ut = Gn(l.stateNode.containerInfo), je(
          l,
          t,
          a
        ), Ut = e;
        break;
      case 22:
        l.memoizedState === null && (e = l.alternate, e !== null && e.memoizedState !== null ? (e = gu, gu = 16777216, je(
          l,
          t,
          a
        ), gu = e) : je(
          l,
          t,
          a
        ));
        break;
      default:
        je(
          l,
          t,
          a
        );
    }
  }
  function oo(l) {
    var t = l.alternate;
    if (t !== null && (l = t.child, l !== null)) {
      t.child = null;
      do
        t = l.sibling, l.sibling = null, l = t;
      while (l !== null);
    }
  }
  function Su(l) {
    var t = l.deletions;
    if ((l.flags & 16) !== 0) {
      if (t !== null)
        for (var a = 0; a < t.length; a++) {
          var e = t[a];
          Xl = e, ho(
            e,
            l
          );
        }
      oo(l);
    }
    if (l.subtreeFlags & 10256)
      for (l = l.child; l !== null; )
        mo(l), l = l.sibling;
  }
  function mo(l) {
    switch (l.tag) {
      case 0:
      case 11:
      case 15:
        Su(l), l.flags & 2048 && Sa(9, l, l.return);
        break;
      case 3:
        Su(l);
        break;
      case 12:
        Su(l);
        break;
      case 22:
        var t = l.stateNode;
        l.memoizedState !== null && t._visibility & 2 && (l.return === null || l.return.tag !== 13) ? (t._visibility &= -3, jn(l)) : Su(l);
        break;
      default:
        Su(l);
    }
  }
  function jn(l) {
    var t = l.deletions;
    if ((l.flags & 16) !== 0) {
      if (t !== null)
        for (var a = 0; a < t.length; a++) {
          var e = t[a];
          Xl = e, ho(
            e,
            l
          );
        }
      oo(l);
    }
    for (l = l.child; l !== null; ) {
      switch (t = l, t.tag) {
        case 0:
        case 11:
        case 15:
          Sa(8, t, t.return), jn(t);
          break;
        case 22:
          a = t.stateNode, a._visibility & 2 && (a._visibility &= -3, jn(t));
          break;
        default:
          jn(t);
      }
      l = l.sibling;
    }
  }
  function ho(l, t) {
    for (; Xl !== null; ) {
      var a = Xl;
      switch (a.tag) {
        case 0:
        case 11:
        case 15:
          Sa(8, a, t);
          break;
        case 23:
        case 22:
          if (a.memoizedState !== null && a.memoizedState.cachePool !== null) {
            var e = a.memoizedState.cachePool.pool;
            e != null && e.refCount++;
          }
          break;
        case 24:
          tu(a.memoizedState.cache);
      }
      if (e = a.child, e !== null) e.return = a, Xl = e;
      else
        l: for (a = l; Xl !== null; ) {
          e = Xl;
          var u = e.sibling, n = e.return;
          if (ao(e), e === a) {
            Xl = null;
            break l;
          }
          if (u !== null) {
            u.return = n, Xl = u;
            break l;
          }
          Xl = n;
        }
    }
  }
  var uv = {
    getCacheForType: function(l) {
      var t = Jl(Ml), a = t.data.get(l);
      return a === void 0 && (a = l(), t.data.set(l, a)), a;
    },
    cacheSignal: function() {
      return Jl(Ml).controller.signal;
    }
  }, nv = typeof WeakMap == "function" ? WeakMap : Map, il = 0, vl = null, $ = null, k = 0, sl = 0, ht = null, ba = !1, _e = !1, Oc = !1, ea = 0, xl = 0, pa = 0, Fa = 0, Dc = 0, vt = 0, Ne = 0, bu = null, nt = null, Uc = !1, _n = 0, vo = 0, Nn = 1 / 0, Mn = null, za = null, ql = 0, Ea = null, Me = null, ua = 0, Hc = 0, Cc = null, yo = null, pu = 0, Rc = null;
  function yt() {
    return (il & 2) !== 0 && k !== 0 ? k & -k : b.T !== null ? Zc() : Of();
  }
  function ro() {
    if (vt === 0)
      if ((k & 536870912) === 0 || I) {
        var l = qu;
        qu <<= 1, (qu & 3932160) === 0 && (qu = 262144), vt = l;
      } else vt = 536870912;
    return l = ot.current, l !== null && (l.flags |= 32), vt;
  }
  function it(l, t, a) {
    (l === vl && (sl === 2 || sl === 9) || l.cancelPendingCommit !== null) && (Oe(l, 0), Aa(
      l,
      k,
      vt,
      !1
    )), Ze(l, a), ((il & 2) === 0 || l !== vl) && (l === vl && ((il & 2) === 0 && (Fa |= a), xl === 4 && Aa(
      l,
      k,
      vt,
      !1
    )), Xt(l));
  }
  function go(l, t, a) {
    if ((il & 6) !== 0) throw Error(g(327));
    var e = !a && (t & 127) === 0 && (t & l.expiredLanes) === 0 || Xe(l, t), u = e ? fv(l, t) : Yc(l, t, !0), n = e;
    do {
      if (u === 0) {
        _e && !e && Aa(l, t, 0, !1);
        break;
      } else {
        if (a = l.current.alternate, n && !iv(a)) {
          u = Yc(l, t, !1), n = !1;
          continue;
        }
        if (u === 2) {
          if (n = t, l.errorRecoveryDisabledLanes & n)
            var i = 0;
          else
            i = l.pendingLanes & -536870913, i = i !== 0 ? i : i & 536870912 ? 536870912 : 0;
          if (i !== 0) {
            t = i;
            l: {
              var c = l;
              u = bu;
              var s = c.current.memoizedState.isDehydrated;
              if (s && (Oe(c, i).flags |= 256), i = Yc(
                c,
                i,
                !1
              ), i !== 2) {
                if (Oc && !s) {
                  c.errorRecoveryDisabledLanes |= n, Fa |= n, u = 4;
                  break l;
                }
                n = nt, nt = u, n !== null && (nt === null ? nt = n : nt.push.apply(
                  nt,
                  n
                ));
              }
              u = i;
            }
            if (n = !1, u !== 2) continue;
          }
        }
        if (u === 1) {
          Oe(l, 0), Aa(l, t, 0, !0);
          break;
        }
        l: {
          switch (e = l, n = u, n) {
            case 0:
            case 1:
              throw Error(g(345));
            case 4:
              if ((t & 4194048) !== t) break;
            case 6:
              Aa(
                e,
                t,
                vt,
                !ba
              );
              break l;
            case 2:
              nt = null;
              break;
            case 3:
            case 5:
              break;
            default:
              throw Error(g(329));
          }
          if ((t & 62914560) === t && (u = _n + 300 - Fl(), 10 < u)) {
            if (Aa(
              e,
              t,
              vt,
              !ba
            ), Bu(e, 0, !0) !== 0) break l;
            ua = t, e.timeoutHandle = $o(
              So.bind(
                null,
                e,
                a,
                nt,
                Mn,
                Uc,
                t,
                vt,
                Fa,
                Ne,
                ba,
                n,
                "Throttled",
                -0,
                0
              ),
              u
            );
            break l;
          }
          So(
            e,
            a,
            nt,
            Mn,
            Uc,
            t,
            vt,
            Fa,
            Ne,
            ba,
            n,
            null,
            -0,
            0
          );
        }
      }
      break;
    } while (!0);
    Xt(l);
  }
  function So(l, t, a, e, u, n, i, c, s, y, z, T, r, S) {
    if (l.timeoutHandle = -1, T = t.subtreeFlags, T & 8192 || (T & 16785408) === 16785408) {
      T = {
        stylesheets: null,
        count: 0,
        imgCount: 0,
        imgBytes: 0,
        suspenseyImages: [],
        waitingForImages: !0,
        waitingForViewTransition: !1,
        unsuspend: Lt
      }, so(
        t,
        n,
        T
      );
      var D = (n & 62914560) === n ? _n - Fl() : (n & 4194048) === n ? vo - Fl() : 0;
      if (D = Vv(
        T,
        D
      ), D !== null) {
        ua = n, l.cancelPendingCommit = D(
          jo.bind(
            null,
            l,
            t,
            n,
            a,
            e,
            u,
            i,
            c,
            s,
            z,
            T,
            null,
            r,
            S
          )
        ), Aa(l, n, i, !y);
        return;
      }
    }
    jo(
      l,
      t,
      n,
      a,
      e,
      u,
      i,
      c,
      s
    );
  }
  function iv(l) {
    for (var t = l; ; ) {
      var a = t.tag;
      if ((a === 0 || a === 11 || a === 15) && t.flags & 16384 && (a = t.updateQueue, a !== null && (a = a.stores, a !== null)))
        for (var e = 0; e < a.length; e++) {
          var u = a[e], n = u.getSnapshot;
          u = u.value;
          try {
            if (!st(n(), u)) return !1;
          } catch {
            return !1;
          }
        }
      if (a = t.child, t.subtreeFlags & 16384 && a !== null)
        a.return = t, t = a;
      else {
        if (t === l) break;
        for (; t.sibling === null; ) {
          if (t.return === null || t.return === l) return !0;
          t = t.return;
        }
        t.sibling.return = t.return, t = t.sibling;
      }
    }
    return !0;
  }
  function Aa(l, t, a, e) {
    t &= ~Dc, t &= ~Fa, l.suspendedLanes |= t, l.pingedLanes &= ~t, e && (l.warmLanes |= t), e = l.expirationTimes;
    for (var u = t; 0 < u; ) {
      var n = 31 - ft(u), i = 1 << n;
      e[n] = -1, u &= ~i;
    }
    a !== 0 && _f(l, a, t);
  }
  function On() {
    return (il & 6) === 0 ? (zu(0), !1) : !0;
  }
  function qc() {
    if ($ !== null) {
      if (sl === 0)
        var l = $.return;
      else
        l = $, wt = Qa = null, Ii(l), pe = null, eu = 0, l = $;
      for (; l !== null; )
        $d(l.alternate, l), l = l.return;
      $ = null;
    }
  }
  function Oe(l, t) {
    var a = l.timeoutHandle;
    a !== -1 && (l.timeoutHandle = -1, jv(a)), a = l.cancelPendingCommit, a !== null && (l.cancelPendingCommit = null, a()), ua = 0, qc(), vl = l, $ = a = Kt(l.current, null), k = t, sl = 0, ht = null, ba = !1, _e = Xe(l, t), Oc = !1, Ne = vt = Dc = Fa = pa = xl = 0, nt = bu = null, Uc = !1, (t & 8) !== 0 && (t |= t & 32);
    var e = l.entangledLanes;
    if (e !== 0)
      for (l = l.entanglements, e &= t; 0 < e; ) {
        var u = 31 - ft(e), n = 1 << u;
        t |= l[u], e &= ~n;
      }
    return ea = t, ku(), a;
  }
  function bo(l, t) {
    L = null, b.H = ou, t === be || t === un ? (t = Cs(), sl = 3) : t === Xi ? (t = Cs(), sl = 4) : sl = t === vc ? 8 : t !== null && typeof t == "object" && typeof t.then == "function" ? 6 : 1, ht = t, $ === null && (xl = 1, bn(
      l,
      At(t, l.current)
    ));
  }
  function po() {
    var l = ot.current;
    return l === null ? !0 : (k & 4194048) === k ? _t === null : (k & 62914560) === k || (k & 536870912) !== 0 ? l === _t : !1;
  }
  function zo() {
    var l = b.H;
    return b.H = ou, l === null ? ou : l;
  }
  function Eo() {
    var l = b.A;
    return b.A = uv, l;
  }
  function Dn() {
    xl = 4, ba || (k & 4194048) !== k && ot.current !== null || (_e = !0), (pa & 134217727) === 0 && (Fa & 134217727) === 0 || vl === null || Aa(
      vl,
      k,
      vt,
      !1
    );
  }
  function Yc(l, t, a) {
    var e = il;
    il |= 2;
    var u = zo(), n = Eo();
    (vl !== l || k !== t) && (Mn = null, Oe(l, t)), t = !1;
    var i = xl;
    l: do
      try {
        if (sl !== 0 && $ !== null) {
          var c = $, s = ht;
          switch (sl) {
            case 8:
              qc(), i = 6;
              break l;
            case 3:
            case 2:
            case 9:
            case 6:
              ot.current === null && (t = !0);
              var y = sl;
              if (sl = 0, ht = null, De(l, c, s, y), a && _e) {
                i = 0;
                break l;
              }
              break;
            default:
              y = sl, sl = 0, ht = null, De(l, c, s, y);
          }
        }
        cv(), i = xl;
        break;
      } catch (z) {
        bo(l, z);
      }
    while (!0);
    return t && l.shellSuspendCounter++, wt = Qa = null, il = e, b.H = u, b.A = n, $ === null && (vl = null, k = 0, ku()), i;
  }
  function cv() {
    for (; $ !== null; ) Ao($);
  }
  function fv(l, t) {
    var a = il;
    il |= 2;
    var e = zo(), u = Eo();
    vl !== l || k !== t ? (Mn = null, Nn = Fl() + 500, Oe(l, t)) : _e = Xe(
      l,
      t
    );
    l: do
      try {
        if (sl !== 0 && $ !== null) {
          t = $;
          var n = ht;
          t: switch (sl) {
            case 1:
              sl = 0, ht = null, De(l, t, n, 1);
              break;
            case 2:
            case 9:
              if (Us(n)) {
                sl = 0, ht = null, To(t);
                break;
              }
              t = function() {
                sl !== 2 && sl !== 9 || vl !== l || (sl = 7), Xt(l);
              }, n.then(t, t);
              break l;
            case 3:
              sl = 7;
              break l;
            case 4:
              sl = 5;
              break l;
            case 7:
              Us(n) ? (sl = 0, ht = null, To(t)) : (sl = 0, ht = null, De(l, t, n, 7));
              break;
            case 5:
              var i = null;
              switch ($.tag) {
                case 26:
                  i = $.memoizedState;
                case 5:
                case 27:
                  var c = $;
                  if (i ? sm(i) : c.stateNode.complete) {
                    sl = 0, ht = null;
                    var s = c.sibling;
                    if (s !== null) $ = s;
                    else {
                      var y = c.return;
                      y !== null ? ($ = y, Un(y)) : $ = null;
                    }
                    break t;
                  }
              }
              sl = 0, ht = null, De(l, t, n, 5);
              break;
            case 6:
              sl = 0, ht = null, De(l, t, n, 6);
              break;
            case 8:
              qc(), xl = 6;
              break l;
            default:
              throw Error(g(462));
          }
        }
        sv();
        break;
      } catch (z) {
        bo(l, z);
      }
    while (!0);
    return wt = Qa = null, b.H = e, b.A = u, il = a, $ !== null ? 0 : (vl = null, k = 0, ku(), xl);
  }
  function sv() {
    for (; $ !== null && !Ye(); )
      Ao($);
  }
  function Ao(l) {
    var t = Jd(l.alternate, l, ea);
    l.memoizedProps = l.pendingProps, t === null ? Un(l) : $ = t;
  }
  function To(l) {
    var t = l, a = t.alternate;
    switch (t.tag) {
      case 15:
      case 0:
        t = Xd(
          a,
          t,
          t.pendingProps,
          t.type,
          void 0,
          k
        );
        break;
      case 11:
        t = Xd(
          a,
          t,
          t.pendingProps,
          t.type.render,
          t.ref,
          k
        );
        break;
      case 5:
        Ii(t);
      default:
        $d(a, t), t = $ = zs(t, ea), t = Jd(a, t, ea);
    }
    l.memoizedProps = l.pendingProps, t === null ? Un(l) : $ = t;
  }
  function De(l, t, a, e) {
    wt = Qa = null, Ii(t), pe = null, eu = 0;
    var u = t.return;
    try {
      if (Fh(
        l,
        u,
        t,
        a,
        k
      )) {
        xl = 1, bn(
          l,
          At(a, l.current)
        ), $ = null;
        return;
      }
    } catch (n) {
      if (u !== null) throw $ = u, n;
      xl = 1, bn(
        l,
        At(a, l.current)
      ), $ = null;
      return;
    }
    t.flags & 32768 ? (I || e === 1 ? l = !0 : _e || (k & 536870912) !== 0 ? l = !1 : (ba = l = !0, (e === 2 || e === 9 || e === 3 || e === 6) && (e = ot.current, e !== null && e.tag === 13 && (e.flags |= 16384))), xo(t, l)) : Un(t);
  }
  function Un(l) {
    var t = l;
    do {
      if ((t.flags & 32768) !== 0) {
        xo(
          t,
          ba
        );
        return;
      }
      l = t.return;
      var a = lv(
        t.alternate,
        t,
        ea
      );
      if (a !== null) {
        $ = a;
        return;
      }
      if (t = t.sibling, t !== null) {
        $ = t;
        return;
      }
      $ = t = l;
    } while (t !== null);
    xl === 0 && (xl = 5);
  }
  function xo(l, t) {
    do {
      var a = tv(l.alternate, l);
      if (a !== null) {
        a.flags &= 32767, $ = a;
        return;
      }
      if (a = l.return, a !== null && (a.flags |= 32768, a.subtreeFlags = 0, a.deletions = null), !t && (l = l.sibling, l !== null)) {
        $ = l;
        return;
      }
      $ = l = a;
    } while (l !== null);
    xl = 6, $ = null;
  }
  function jo(l, t, a, e, u, n, i, c, s) {
    l.cancelPendingCommit = null;
    do
      Hn();
    while (ql !== 0);
    if ((il & 6) !== 0) throw Error(g(327));
    if (t !== null) {
      if (t === l.current) throw Error(g(177));
      if (n = t.lanes | t.childLanes, n |= xi, Qm(
        l,
        a,
        n,
        i,
        c,
        s
      ), l === vl && ($ = vl = null, k = 0), Me = t, Ea = l, ua = a, Hc = n, Cc = u, yo = e, (t.subtreeFlags & 10256) !== 0 || (t.flags & 10256) !== 0 ? (l.callbackNode = null, l.callbackPriority = 0, hv(Cu, function() {
        return Do(), null;
      })) : (l.callbackNode = null, l.callbackPriority = 0), e = (t.flags & 13878) !== 0, (t.subtreeFlags & 13878) !== 0 || e) {
        e = b.T, b.T = null, u = _.p, _.p = 2, i = il, il |= 4;
        try {
          av(l, t, a);
        } finally {
          il = i, _.p = u, b.T = e;
        }
      }
      ql = 1, _o(), No(), Mo();
    }
  }
  function _o() {
    if (ql === 1) {
      ql = 0;
      var l = Ea, t = Me, a = (t.flags & 13878) !== 0;
      if ((t.subtreeFlags & 13878) !== 0 || a) {
        a = b.T, b.T = null;
        var e = _.p;
        _.p = 2;
        var u = il;
        il |= 4;
        try {
          io(t, l);
          var n = Wc, i = ms(l.containerInfo), c = n.focusedElem, s = n.selectionRange;
          if (i !== c && c && c.ownerDocument && os(
            c.ownerDocument.documentElement,
            c
          )) {
            if (s !== null && pi(c)) {
              var y = s.start, z = s.end;
              if (z === void 0 && (z = y), "selectionStart" in c)
                c.selectionStart = y, c.selectionEnd = Math.min(
                  z,
                  c.value.length
                );
              else {
                var T = c.ownerDocument || document, r = T && T.defaultView || window;
                if (r.getSelection) {
                  var S = r.getSelection(), D = c.textContent.length, B = Math.min(s.start, D), hl = s.end === void 0 ? B : Math.min(s.end, D);
                  !S.extend && B > hl && (i = hl, hl = B, B = i);
                  var h = ds(
                    c,
                    B
                  ), m = ds(
                    c,
                    hl
                  );
                  if (h && m && (S.rangeCount !== 1 || S.anchorNode !== h.node || S.anchorOffset !== h.offset || S.focusNode !== m.node || S.focusOffset !== m.offset)) {
                    var v = T.createRange();
                    v.setStart(h.node, h.offset), S.removeAllRanges(), B > hl ? (S.addRange(v), S.extend(m.node, m.offset)) : (v.setEnd(m.node, m.offset), S.addRange(v));
                  }
                }
              }
            }
            for (T = [], S = c; S = S.parentNode; )
              S.nodeType === 1 && T.push({
                element: S,
                left: S.scrollLeft,
                top: S.scrollTop
              });
            for (typeof c.focus == "function" && c.focus(), c = 0; c < T.length; c++) {
              var A = T[c];
              A.element.scrollLeft = A.left, A.element.scrollTop = A.top;
            }
          }
          Kn = !!$c, Wc = $c = null;
        } finally {
          il = u, _.p = e, b.T = a;
        }
      }
      l.current = t, ql = 2;
    }
  }
  function No() {
    if (ql === 2) {
      ql = 0;
      var l = Ea, t = Me, a = (t.flags & 8772) !== 0;
      if ((t.subtreeFlags & 8772) !== 0 || a) {
        a = b.T, b.T = null;
        var e = _.p;
        _.p = 2;
        var u = il;
        il |= 4;
        try {
          to(l, t.alternate, t);
        } finally {
          il = u, _.p = e, b.T = a;
        }
      }
      ql = 3;
    }
  }
  function Mo() {
    if (ql === 4 || ql === 3) {
      ql = 0, Be();
      var l = Ea, t = Me, a = ua, e = yo;
      (t.subtreeFlags & 10256) !== 0 || (t.flags & 10256) !== 0 ? ql = 5 : (ql = 0, Me = Ea = null, Oo(l, l.pendingLanes));
      var u = l.pendingLanes;
      if (u === 0 && (za = null), ti(a), t = t.stateNode, ct && typeof ct.onCommitFiberRoot == "function")
        try {
          ct.onCommitFiberRoot(
            Ge,
            t,
            void 0,
            (t.current.flags & 128) === 128
          );
        } catch {
        }
      if (e !== null) {
        t = b.T, u = _.p, _.p = 2, b.T = null;
        try {
          for (var n = l.onRecoverableError, i = 0; i < e.length; i++) {
            var c = e[i];
            n(c.value, {
              componentStack: c.stack
            });
          }
        } finally {
          b.T = t, _.p = u;
        }
      }
      (ua & 3) !== 0 && Hn(), Xt(l), u = l.pendingLanes, (a & 261930) !== 0 && (u & 42) !== 0 ? l === Rc ? pu++ : (pu = 0, Rc = l) : pu = 0, zu(0);
    }
  }
  function Oo(l, t) {
    (l.pooledCacheLanes &= t) === 0 && (t = l.pooledCache, t != null && (l.pooledCache = null, tu(t)));
  }
  function Hn() {
    return _o(), No(), Mo(), Do();
  }
  function Do() {
    if (ql !== 5) return !1;
    var l = Ea, t = Hc;
    Hc = 0;
    var a = ti(ua), e = b.T, u = _.p;
    try {
      _.p = 32 > a ? 32 : a, b.T = null, a = Cc, Cc = null;
      var n = Ea, i = ua;
      if (ql = 0, Me = Ea = null, ua = 0, (il & 6) !== 0) throw Error(g(331));
      var c = il;
      if (il |= 4, mo(n.current), fo(
        n,
        n.current,
        i,
        a
      ), il = c, zu(0, !1), ct && typeof ct.onPostCommitFiberRoot == "function")
        try {
          ct.onPostCommitFiberRoot(Ge, n);
        } catch {
        }
      return !0;
    } finally {
      _.p = u, b.T = e, Oo(l, t);
    }
  }
  function Uo(l, t, a) {
    t = At(a, t), t = hc(l.stateNode, t, 2), l = ya(l, t, 2), l !== null && (Ze(l, 2), Xt(l));
  }
  function dl(l, t, a) {
    if (l.tag === 3)
      Uo(l, l, a);
    else
      for (; t !== null; ) {
        if (t.tag === 3) {
          Uo(
            t,
            l,
            a
          );
          break;
        } else if (t.tag === 1) {
          var e = t.stateNode;
          if (typeof t.type.getDerivedStateFromError == "function" || typeof e.componentDidCatch == "function" && (za === null || !za.has(e))) {
            l = At(a, l), a = Ud(2), e = ya(t, a, 2), e !== null && (Hd(
              a,
              e,
              t,
              l
            ), Ze(e, 2), Xt(e));
            break;
          }
        }
        t = t.return;
      }
  }
  function Bc(l, t, a) {
    var e = l.pingCache;
    if (e === null) {
      e = l.pingCache = new nv();
      var u = /* @__PURE__ */ new Set();
      e.set(t, u);
    } else
      u = e.get(t), u === void 0 && (u = /* @__PURE__ */ new Set(), e.set(t, u));
    u.has(a) || (Oc = !0, u.add(a), l = dv.bind(null, l, t, a), t.then(l, l));
  }
  function dv(l, t, a) {
    var e = l.pingCache;
    e !== null && e.delete(t), l.pingedLanes |= l.suspendedLanes & a, l.warmLanes &= ~a, vl === l && (k & a) === a && (xl === 4 || xl === 3 && (k & 62914560) === k && 300 > Fl() - _n ? (il & 2) === 0 && Oe(l, 0) : Dc |= a, Ne === k && (Ne = 0)), Xt(l);
  }
  function Ho(l, t) {
    t === 0 && (t = jf()), l = Ga(l, t), l !== null && (Ze(l, t), Xt(l));
  }
  function ov(l) {
    var t = l.memoizedState, a = 0;
    t !== null && (a = t.retryLane), Ho(l, a);
  }
  function mv(l, t) {
    var a = 0;
    switch (l.tag) {
      case 31:
      case 13:
        var e = l.stateNode, u = l.memoizedState;
        u !== null && (a = u.retryLane);
        break;
      case 19:
        e = l.stateNode;
        break;
      case 22:
        e = l.stateNode._retryCache;
        break;
      default:
        throw Error(g(314));
    }
    e !== null && e.delete(t), Ho(l, a);
  }
  function hv(l, t) {
    return tl(l, t);
  }
  var Cn = null, Ue = null, Gc = !1, Rn = !1, Xc = !1, Ta = 0;
  function Xt(l) {
    l !== Ue && l.next === null && (Ue === null ? Cn = Ue = l : Ue = Ue.next = l), Rn = !0, Gc || (Gc = !0, yv());
  }
  function zu(l, t) {
    if (!Xc && Rn) {
      Xc = !0;
      do
        for (var a = !1, e = Cn; e !== null; ) {
          if (l !== 0) {
            var u = e.pendingLanes;
            if (u === 0) var n = 0;
            else {
              var i = e.suspendedLanes, c = e.pingedLanes;
              n = (1 << 31 - ft(42 | l) + 1) - 1, n &= u & ~(i & ~c), n = n & 201326741 ? n & 201326741 | 1 : n ? n | 2 : 0;
            }
            n !== 0 && (a = !0, Yo(e, n));
          } else
            n = k, n = Bu(
              e,
              e === vl ? n : 0,
              e.cancelPendingCommit !== null || e.timeoutHandle !== -1
            ), (n & 3) === 0 || Xe(e, n) || (a = !0, Yo(e, n));
          e = e.next;
        }
      while (a);
      Xc = !1;
    }
  }
  function vv() {
    Co();
  }
  function Co() {
    Rn = Gc = !1;
    var l = 0;
    Ta !== 0 && xv() && (l = Ta);
    for (var t = Fl(), a = null, e = Cn; e !== null; ) {
      var u = e.next, n = Ro(e, t);
      n === 0 ? (e.next = null, a === null ? Cn = u : a.next = u, u === null && (Ue = a)) : (a = e, (l !== 0 || (n & 3) !== 0) && (Rn = !0)), e = u;
    }
    ql !== 0 && ql !== 5 || zu(l), Ta !== 0 && (Ta = 0);
  }
  function Ro(l, t) {
    for (var a = l.suspendedLanes, e = l.pingedLanes, u = l.expirationTimes, n = l.pendingLanes & -62914561; 0 < n; ) {
      var i = 31 - ft(n), c = 1 << i, s = u[i];
      s === -1 ? ((c & a) === 0 || (c & e) !== 0) && (u[i] = Zm(c, t)) : s <= t && (l.expiredLanes |= c), n &= ~c;
    }
    if (t = vl, a = k, a = Bu(
      l,
      l === t ? a : 0,
      l.cancelPendingCommit !== null || l.timeoutHandle !== -1
    ), e = l.callbackNode, a === 0 || l === t && (sl === 2 || sl === 9) || l.cancelPendingCommit !== null)
      return e !== null && e !== null && Ha(e), l.callbackNode = null, l.callbackPriority = 0;
    if ((a & 3) === 0 || Xe(l, a)) {
      if (t = a & -a, t === l.callbackPriority) return t;
      switch (e !== null && Ha(e), ti(a)) {
        case 2:
        case 8:
          a = Tf;
          break;
        case 32:
          a = Cu;
          break;
        case 268435456:
          a = xf;
          break;
        default:
          a = Cu;
      }
      return e = qo.bind(null, l), a = tl(a, e), l.callbackPriority = t, l.callbackNode = a, t;
    }
    return e !== null && e !== null && Ha(e), l.callbackPriority = 2, l.callbackNode = null, 2;
  }
  function qo(l, t) {
    if (ql !== 0 && ql !== 5)
      return l.callbackNode = null, l.callbackPriority = 0, null;
    var a = l.callbackNode;
    if (Hn() && l.callbackNode !== a)
      return null;
    var e = k;
    return e = Bu(
      l,
      l === vl ? e : 0,
      l.cancelPendingCommit !== null || l.timeoutHandle !== -1
    ), e === 0 ? null : (go(l, e, t), Ro(l, Fl()), l.callbackNode != null && l.callbackNode === a ? qo.bind(null, l) : null);
  }
  function Yo(l, t) {
    if (Hn()) return null;
    go(l, t, !0);
  }
  function yv() {
    _v(function() {
      (il & 6) !== 0 ? tl(
        Af,
        vv
      ) : Co();
    });
  }
  function Zc() {
    if (Ta === 0) {
      var l = ge;
      l === 0 && (l = Ru, Ru <<= 1, (Ru & 261888) === 0 && (Ru = 256)), Ta = l;
    }
    return Ta;
  }
  function Bo(l) {
    return l == null || typeof l == "symbol" || typeof l == "boolean" ? null : typeof l == "function" ? l : Qu("" + l);
  }
  function Go(l, t) {
    var a = t.ownerDocument.createElement("input");
    return a.name = t.name, a.value = t.value, l.id && a.setAttribute("form", l.id), t.parentNode.insertBefore(a, t), l = new FormData(l), a.parentNode.removeChild(a), l;
  }
  function rv(l, t, a, e, u) {
    if (t === "submit" && a && a.stateNode === u) {
      var n = Bo(
        (u[lt] || null).action
      ), i = e.submitter;
      i && (t = (t = i[lt] || null) ? Bo(t.formAction) : i.getAttribute("formAction"), t !== null && (n = t, i = null));
      var c = new Ju(
        "action",
        "action",
        null,
        e,
        u
      );
      l.push({
        event: c,
        listeners: [
          {
            instance: null,
            listener: function() {
              if (e.defaultPrevented) {
                if (Ta !== 0) {
                  var s = i ? Go(u, i) : new FormData(u);
                  cc(
                    a,
                    {
                      pending: !0,
                      data: s,
                      method: u.method,
                      action: n
                    },
                    null,
                    s
                  );
                }
              } else
                typeof n == "function" && (c.preventDefault(), s = i ? Go(u, i) : new FormData(u), cc(
                  a,
                  {
                    pending: !0,
                    data: s,
                    method: u.method,
                    action: n
                  },
                  n,
                  s
                ));
            },
            currentTarget: u
          }
        ]
      });
    }
  }
  for (var Qc = 0; Qc < Ti.length; Qc++) {
    var Lc = Ti[Qc], gv = Lc.toLowerCase(), Sv = Lc[0].toUpperCase() + Lc.slice(1);
    Dt(
      gv,
      "on" + Sv
    );
  }
  Dt(ys, "onAnimationEnd"), Dt(rs, "onAnimationIteration"), Dt(gs, "onAnimationStart"), Dt("dblclick", "onDoubleClick"), Dt("focusin", "onFocus"), Dt("focusout", "onBlur"), Dt(Ch, "onTransitionRun"), Dt(Rh, "onTransitionStart"), Dt(qh, "onTransitionCancel"), Dt(Ss, "onTransitionEnd"), ee("onMouseEnter", ["mouseout", "mouseover"]), ee("onMouseLeave", ["mouseout", "mouseover"]), ee("onPointerEnter", ["pointerout", "pointerover"]), ee("onPointerLeave", ["pointerout", "pointerover"]), Ra(
    "onChange",
    "change click focusin focusout input keydown keyup selectionchange".split(" ")
  ), Ra(
    "onSelect",
    "focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(
      " "
    )
  ), Ra("onBeforeInput", [
    "compositionend",
    "keypress",
    "textInput",
    "paste"
  ]), Ra(
    "onCompositionEnd",
    "compositionend focusout keydown keypress keyup mousedown".split(" ")
  ), Ra(
    "onCompositionStart",
    "compositionstart focusout keydown keypress keyup mousedown".split(" ")
  ), Ra(
    "onCompositionUpdate",
    "compositionupdate focusout keydown keypress keyup mousedown".split(" ")
  );
  var Eu = "abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(
    " "
  ), bv = new Set(
    "beforetoggle cancel close invalid load scroll scrollend toggle".split(" ").concat(Eu)
  );
  function Xo(l, t) {
    t = (t & 4) !== 0;
    for (var a = 0; a < l.length; a++) {
      var e = l[a], u = e.event;
      e = e.listeners;
      l: {
        var n = void 0;
        if (t)
          for (var i = e.length - 1; 0 <= i; i--) {
            var c = e[i], s = c.instance, y = c.currentTarget;
            if (c = c.listener, s !== n && u.isPropagationStopped())
              break l;
            n = c, u.currentTarget = y;
            try {
              n(u);
            } catch (z) {
              Wu(z);
            }
            u.currentTarget = null, n = s;
          }
        else
          for (i = 0; i < e.length; i++) {
            if (c = e[i], s = c.instance, y = c.currentTarget, c = c.listener, s !== n && u.isPropagationStopped())
              break l;
            n = c, u.currentTarget = y;
            try {
              n(u);
            } catch (z) {
              Wu(z);
            }
            u.currentTarget = null, n = s;
          }
      }
    }
  }
  function W(l, t) {
    var a = t[ai];
    a === void 0 && (a = t[ai] = /* @__PURE__ */ new Set());
    var e = l + "__bubble";
    a.has(e) || (Zo(t, l, 2, !1), a.add(e));
  }
  function Vc(l, t, a) {
    var e = 0;
    t && (e |= 4), Zo(
      a,
      l,
      e,
      t
    );
  }
  var qn = "_reactListening" + Math.random().toString(36).slice(2);
  function Kc(l) {
    if (!l[qn]) {
      l[qn] = !0, Hf.forEach(function(a) {
        a !== "selectionchange" && (bv.has(a) || Vc(a, !1, l), Vc(a, !0, l));
      });
      var t = l.nodeType === 9 ? l : l.ownerDocument;
      t === null || t[qn] || (t[qn] = !0, Vc("selectionchange", !1, t));
    }
  }
  function Zo(l, t, a, e) {
    switch (rm(t)) {
      case 2:
        var u = wv;
        break;
      case 8:
        u = $v;
        break;
      default:
        u = cf;
    }
    a = u.bind(
      null,
      t,
      a,
      l
    ), u = void 0, !oi || t !== "touchstart" && t !== "touchmove" && t !== "wheel" || (u = !0), e ? u !== void 0 ? l.addEventListener(t, a, {
      capture: !0,
      passive: u
    }) : l.addEventListener(t, a, !0) : u !== void 0 ? l.addEventListener(t, a, {
      passive: u
    }) : l.addEventListener(t, a, !1);
  }
  function Jc(l, t, a, e, u) {
    var n = e;
    if ((t & 1) === 0 && (t & 2) === 0 && e !== null)
      l: for (; ; ) {
        if (e === null) return;
        var i = e.tag;
        if (i === 3 || i === 4) {
          var c = e.stateNode.containerInfo;
          if (c === u) break;
          if (i === 4)
            for (i = e.return; i !== null; ) {
              var s = i.tag;
              if ((s === 3 || s === 4) && i.stateNode.containerInfo === u)
                return;
              i = i.return;
            }
          for (; c !== null; ) {
            if (i = le(c), i === null) return;
            if (s = i.tag, s === 5 || s === 6 || s === 26 || s === 27) {
              e = n = i;
              continue l;
            }
            c = c.parentNode;
          }
        }
        e = e.return;
      }
    Kf(function() {
      var y = n, z = si(a), T = [];
      l: {
        var r = bs.get(l);
        if (r !== void 0) {
          var S = Ju, D = l;
          switch (l) {
            case "keypress":
              if (Vu(a) === 0) break l;
            case "keydown":
            case "keyup":
              S = mh;
              break;
            case "focusin":
              D = "focus", S = yi;
              break;
            case "focusout":
              D = "blur", S = yi;
              break;
            case "beforeblur":
            case "afterblur":
              S = yi;
              break;
            case "click":
              if (a.button === 2) break l;
            case "auxclick":
            case "dblclick":
            case "mousedown":
            case "mousemove":
            case "mouseup":
            case "mouseout":
            case "mouseover":
            case "contextmenu":
              S = $f;
              break;
            case "drag":
            case "dragend":
            case "dragenter":
            case "dragexit":
            case "dragleave":
            case "dragover":
            case "dragstart":
            case "drop":
              S = lh;
              break;
            case "touchcancel":
            case "touchend":
            case "touchmove":
            case "touchstart":
              S = yh;
              break;
            case ys:
            case rs:
            case gs:
              S = eh;
              break;
            case Ss:
              S = gh;
              break;
            case "scroll":
            case "scrollend":
              S = Im;
              break;
            case "wheel":
              S = bh;
              break;
            case "copy":
            case "cut":
            case "paste":
              S = nh;
              break;
            case "gotpointercapture":
            case "lostpointercapture":
            case "pointercancel":
            case "pointerdown":
            case "pointermove":
            case "pointerout":
            case "pointerover":
            case "pointerup":
              S = kf;
              break;
            case "toggle":
            case "beforetoggle":
              S = zh;
          }
          var B = (t & 4) !== 0, hl = !B && (l === "scroll" || l === "scrollend"), h = B ? r !== null ? r + "Capture" : null : r;
          B = [];
          for (var m = y, v; m !== null; ) {
            var A = m;
            if (v = A.stateNode, A = A.tag, A !== 5 && A !== 26 && A !== 27 || v === null || h === null || (A = Ve(m, h), A != null && B.push(
              Au(m, A, v)
            )), hl) break;
            m = m.return;
          }
          0 < B.length && (r = new S(
            r,
            D,
            null,
            a,
            z
          ), T.push({ event: r, listeners: B }));
        }
      }
      if ((t & 7) === 0) {
        l: {
          if (r = l === "mouseover" || l === "pointerover", S = l === "mouseout" || l === "pointerout", r && a !== fi && (D = a.relatedTarget || a.fromElement) && (le(D) || D[Pa]))
            break l;
          if ((S || r) && (r = z.window === z ? z : (r = z.ownerDocument) ? r.defaultView || r.parentWindow : window, S ? (D = a.relatedTarget || a.toElement, S = y, D = D ? le(D) : null, D !== null && (hl = G(D), B = D.tag, D !== hl || B !== 5 && B !== 27 && B !== 6) && (D = null)) : (S = null, D = y), S !== D)) {
            if (B = $f, A = "onMouseLeave", h = "onMouseEnter", m = "mouse", (l === "pointerout" || l === "pointerover") && (B = kf, A = "onPointerLeave", h = "onPointerEnter", m = "pointer"), hl = S == null ? r : Le(S), v = D == null ? r : Le(D), r = new B(
              A,
              m + "leave",
              S,
              a,
              z
            ), r.target = hl, r.relatedTarget = v, A = null, le(z) === y && (B = new B(
              h,
              m + "enter",
              D,
              a,
              z
            ), B.target = v, B.relatedTarget = hl, A = B), hl = A, S && D)
              t: {
                for (B = pv, h = S, m = D, v = 0, A = h; A; A = B(A))
                  v++;
                A = 0;
                for (var q = m; q; q = B(q))
                  A++;
                for (; 0 < v - A; )
                  h = B(h), v--;
                for (; 0 < A - v; )
                  m = B(m), A--;
                for (; v--; ) {
                  if (h === m || m !== null && h === m.alternate) {
                    B = h;
                    break t;
                  }
                  h = B(h), m = B(m);
                }
                B = null;
              }
            else B = null;
            S !== null && Qo(
              T,
              r,
              S,
              B,
              !1
            ), D !== null && hl !== null && Qo(
              T,
              hl,
              D,
              B,
              !0
            );
          }
        }
        l: {
          if (r = y ? Le(y) : window, S = r.nodeName && r.nodeName.toLowerCase(), S === "select" || S === "input" && r.type === "file")
            var el = us;
          else if (as(r))
            if (ns)
              el = Dh;
            else {
              el = Mh;
              var H = Nh;
            }
          else
            S = r.nodeName, !S || S.toLowerCase() !== "input" || r.type !== "checkbox" && r.type !== "radio" ? y && ci(y.elementType) && (el = us) : el = Oh;
          if (el && (el = el(l, y))) {
            es(
              T,
              el,
              a,
              z
            );
            break l;
          }
          H && H(l, r, y), l === "focusout" && y && r.type === "number" && y.memoizedProps.value != null && ii(r, "number", r.value);
        }
        switch (H = y ? Le(y) : window, l) {
          case "focusin":
            (as(H) || H.contentEditable === "true") && (se = H, zi = y, Ie = null);
            break;
          case "focusout":
            Ie = zi = se = null;
            break;
          case "mousedown":
            Ei = !0;
            break;
          case "contextmenu":
          case "mouseup":
          case "dragend":
            Ei = !1, hs(T, a, z);
            break;
          case "selectionchange":
            if (Hh) break;
          case "keydown":
          case "keyup":
            hs(T, a, z);
        }
        var V;
        if (gi)
          l: {
            switch (l) {
              case "compositionstart":
                var F = "onCompositionStart";
                break l;
              case "compositionend":
                F = "onCompositionEnd";
                break l;
              case "compositionupdate":
                F = "onCompositionUpdate";
                break l;
            }
            F = void 0;
          }
        else
          fe ? ls(l, a) && (F = "onCompositionEnd") : l === "keydown" && a.keyCode === 229 && (F = "onCompositionStart");
        F && (Ff && a.locale !== "ko" && (fe || F !== "onCompositionStart" ? F === "onCompositionEnd" && fe && (V = Jf()) : (fa = z, mi = "value" in fa ? fa.value : fa.textContent, fe = !0)), H = Yn(y, F), 0 < H.length && (F = new Wf(
          F,
          l,
          null,
          a,
          z
        ), T.push({ event: F, listeners: H }), V ? F.data = V : (V = ts(a), V !== null && (F.data = V)))), (V = Ah ? Th(l, a) : xh(l, a)) && (F = Yn(y, "onBeforeInput"), 0 < F.length && (H = new Wf(
          "onBeforeInput",
          "beforeinput",
          null,
          a,
          z
        ), T.push({
          event: H,
          listeners: F
        }), H.data = V)), rv(
          T,
          l,
          y,
          a,
          z
        );
      }
      Xo(T, t);
    });
  }
  function Au(l, t, a) {
    return {
      instance: l,
      listener: t,
      currentTarget: a
    };
  }
  function Yn(l, t) {
    for (var a = t + "Capture", e = []; l !== null; ) {
      var u = l, n = u.stateNode;
      if (u = u.tag, u !== 5 && u !== 26 && u !== 27 || n === null || (u = Ve(l, a), u != null && e.unshift(
        Au(l, u, n)
      ), u = Ve(l, t), u != null && e.push(
        Au(l, u, n)
      )), l.tag === 3) return e;
      l = l.return;
    }
    return [];
  }
  function pv(l) {
    if (l === null) return null;
    do
      l = l.return;
    while (l && l.tag !== 5 && l.tag !== 27);
    return l || null;
  }
  function Qo(l, t, a, e, u) {
    for (var n = t._reactName, i = []; a !== null && a !== e; ) {
      var c = a, s = c.alternate, y = c.stateNode;
      if (c = c.tag, s !== null && s === e) break;
      c !== 5 && c !== 26 && c !== 27 || y === null || (s = y, u ? (y = Ve(a, n), y != null && i.unshift(
        Au(a, y, s)
      )) : u || (y = Ve(a, n), y != null && i.push(
        Au(a, y, s)
      ))), a = a.return;
    }
    i.length !== 0 && l.push({ event: t, listeners: i });
  }
  var zv = /\r\n?/g, Ev = /\u0000|\uFFFD/g;
  function Lo(l) {
    return (typeof l == "string" ? l : "" + l).replace(zv, `
`).replace(Ev, "");
  }
  function Vo(l, t) {
    return t = Lo(t), Lo(l) === t;
  }
  function ml(l, t, a, e, u, n) {
    switch (a) {
      case "children":
        typeof e == "string" ? t === "body" || t === "textarea" && e === "" || ne(l, e) : (typeof e == "number" || typeof e == "bigint") && t !== "body" && ne(l, "" + e);
        break;
      case "className":
        Xu(l, "class", e);
        break;
      case "tabIndex":
        Xu(l, "tabindex", e);
        break;
      case "dir":
      case "role":
      case "viewBox":
      case "width":
      case "height":
        Xu(l, a, e);
        break;
      case "style":
        Lf(l, e, n);
        break;
      case "data":
        if (t !== "object") {
          Xu(l, "data", e);
          break;
        }
      case "src":
      case "href":
        if (e === "" && (t !== "a" || a !== "href")) {
          l.removeAttribute(a);
          break;
        }
        if (e == null || typeof e == "function" || typeof e == "symbol" || typeof e == "boolean") {
          l.removeAttribute(a);
          break;
        }
        e = Qu("" + e), l.setAttribute(a, e);
        break;
      case "action":
      case "formAction":
        if (typeof e == "function") {
          l.setAttribute(
            a,
            "javascript:throw new Error('A React form was unexpectedly submitted. If you called form.submit() manually, consider using form.requestSubmit() instead. If you\\'re trying to use event.stopPropagation() in a submit event handler, consider also calling event.preventDefault().')"
          );
          break;
        } else
          typeof n == "function" && (a === "formAction" ? (t !== "input" && ml(l, t, "name", u.name, u, null), ml(
            l,
            t,
            "formEncType",
            u.formEncType,
            u,
            null
          ), ml(
            l,
            t,
            "formMethod",
            u.formMethod,
            u,
            null
          ), ml(
            l,
            t,
            "formTarget",
            u.formTarget,
            u,
            null
          )) : (ml(l, t, "encType", u.encType, u, null), ml(l, t, "method", u.method, u, null), ml(l, t, "target", u.target, u, null)));
        if (e == null || typeof e == "symbol" || typeof e == "boolean") {
          l.removeAttribute(a);
          break;
        }
        e = Qu("" + e), l.setAttribute(a, e);
        break;
      case "onClick":
        e != null && (l.onclick = Lt);
        break;
      case "onScroll":
        e != null && W("scroll", l);
        break;
      case "onScrollEnd":
        e != null && W("scrollend", l);
        break;
      case "dangerouslySetInnerHTML":
        if (e != null) {
          if (typeof e != "object" || !("__html" in e))
            throw Error(g(61));
          if (a = e.__html, a != null) {
            if (u.children != null) throw Error(g(60));
            l.innerHTML = a;
          }
        }
        break;
      case "multiple":
        l.multiple = e && typeof e != "function" && typeof e != "symbol";
        break;
      case "muted":
        l.muted = e && typeof e != "function" && typeof e != "symbol";
        break;
      case "suppressContentEditableWarning":
      case "suppressHydrationWarning":
      case "defaultValue":
      case "defaultChecked":
      case "innerHTML":
      case "ref":
        break;
      case "autoFocus":
        break;
      case "xlinkHref":
        if (e == null || typeof e == "function" || typeof e == "boolean" || typeof e == "symbol") {
          l.removeAttribute("xlink:href");
          break;
        }
        a = Qu("" + e), l.setAttributeNS(
          "http://www.w3.org/1999/xlink",
          "xlink:href",
          a
        );
        break;
      case "contentEditable":
      case "spellCheck":
      case "draggable":
      case "value":
      case "autoReverse":
      case "externalResourcesRequired":
      case "focusable":
      case "preserveAlpha":
        e != null && typeof e != "function" && typeof e != "symbol" ? l.setAttribute(a, "" + e) : l.removeAttribute(a);
        break;
      case "inert":
      case "allowFullScreen":
      case "async":
      case "autoPlay":
      case "controls":
      case "default":
      case "defer":
      case "disabled":
      case "disablePictureInPicture":
      case "disableRemotePlayback":
      case "formNoValidate":
      case "hidden":
      case "loop":
      case "noModule":
      case "noValidate":
      case "open":
      case "playsInline":
      case "readOnly":
      case "required":
      case "reversed":
      case "scoped":
      case "seamless":
      case "itemScope":
        e && typeof e != "function" && typeof e != "symbol" ? l.setAttribute(a, "") : l.removeAttribute(a);
        break;
      case "capture":
      case "download":
        e === !0 ? l.setAttribute(a, "") : e !== !1 && e != null && typeof e != "function" && typeof e != "symbol" ? l.setAttribute(a, e) : l.removeAttribute(a);
        break;
      case "cols":
      case "rows":
      case "size":
      case "span":
        e != null && typeof e != "function" && typeof e != "symbol" && !isNaN(e) && 1 <= e ? l.setAttribute(a, e) : l.removeAttribute(a);
        break;
      case "rowSpan":
      case "start":
        e == null || typeof e == "function" || typeof e == "symbol" || isNaN(e) ? l.removeAttribute(a) : l.setAttribute(a, e);
        break;
      case "popover":
        W("beforetoggle", l), W("toggle", l), Gu(l, "popover", e);
        break;
      case "xlinkActuate":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:actuate",
          e
        );
        break;
      case "xlinkArcrole":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:arcrole",
          e
        );
        break;
      case "xlinkRole":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:role",
          e
        );
        break;
      case "xlinkShow":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:show",
          e
        );
        break;
      case "xlinkTitle":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:title",
          e
        );
        break;
      case "xlinkType":
        Qt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:type",
          e
        );
        break;
      case "xmlBase":
        Qt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:base",
          e
        );
        break;
      case "xmlLang":
        Qt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:lang",
          e
        );
        break;
      case "xmlSpace":
        Qt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:space",
          e
        );
        break;
      case "is":
        Gu(l, "is", e);
        break;
      case "innerText":
      case "textContent":
        break;
      default:
        (!(2 < a.length) || a[0] !== "o" && a[0] !== "O" || a[1] !== "n" && a[1] !== "N") && (a = km.get(a) || a, Gu(l, a, e));
    }
  }
  function wc(l, t, a, e, u, n) {
    switch (a) {
      case "style":
        Lf(l, e, n);
        break;
      case "dangerouslySetInnerHTML":
        if (e != null) {
          if (typeof e != "object" || !("__html" in e))
            throw Error(g(61));
          if (a = e.__html, a != null) {
            if (u.children != null) throw Error(g(60));
            l.innerHTML = a;
          }
        }
        break;
      case "children":
        typeof e == "string" ? ne(l, e) : (typeof e == "number" || typeof e == "bigint") && ne(l, "" + e);
        break;
      case "onScroll":
        e != null && W("scroll", l);
        break;
      case "onScrollEnd":
        e != null && W("scrollend", l);
        break;
      case "onClick":
        e != null && (l.onclick = Lt);
        break;
      case "suppressContentEditableWarning":
      case "suppressHydrationWarning":
      case "innerHTML":
      case "ref":
        break;
      case "innerText":
      case "textContent":
        break;
      default:
        if (!Cf.hasOwnProperty(a))
          l: {
            if (a[0] === "o" && a[1] === "n" && (u = a.endsWith("Capture"), t = a.slice(2, u ? a.length - 7 : void 0), n = l[lt] || null, n = n != null ? n[a] : null, typeof n == "function" && l.removeEventListener(t, n, u), typeof e == "function")) {
              typeof n != "function" && n !== null && (a in l ? l[a] = null : l.hasAttribute(a) && l.removeAttribute(a)), l.addEventListener(t, e, u);
              break l;
            }
            a in l ? l[a] = e : e === !0 ? l.setAttribute(a, "") : Gu(l, a, e);
          }
    }
  }
  function $l(l, t, a) {
    switch (t) {
      case "div":
      case "span":
      case "svg":
      case "path":
      case "a":
      case "g":
      case "p":
      case "li":
        break;
      case "img":
        W("error", l), W("load", l);
        var e = !1, u = !1, n;
        for (n in a)
          if (a.hasOwnProperty(n)) {
            var i = a[n];
            if (i != null)
              switch (n) {
                case "src":
                  e = !0;
                  break;
                case "srcSet":
                  u = !0;
                  break;
                case "children":
                case "dangerouslySetInnerHTML":
                  throw Error(g(137, t));
                default:
                  ml(l, t, n, i, a, null);
              }
          }
        u && ml(l, t, "srcSet", a.srcSet, a, null), e && ml(l, t, "src", a.src, a, null);
        return;
      case "input":
        W("invalid", l);
        var c = n = i = u = null, s = null, y = null;
        for (e in a)
          if (a.hasOwnProperty(e)) {
            var z = a[e];
            if (z != null)
              switch (e) {
                case "name":
                  u = z;
                  break;
                case "type":
                  i = z;
                  break;
                case "checked":
                  s = z;
                  break;
                case "defaultChecked":
                  y = z;
                  break;
                case "value":
                  n = z;
                  break;
                case "defaultValue":
                  c = z;
                  break;
                case "children":
                case "dangerouslySetInnerHTML":
                  if (z != null)
                    throw Error(g(137, t));
                  break;
                default:
                  ml(l, t, e, z, a, null);
              }
          }
        Gf(
          l,
          n,
          c,
          s,
          y,
          i,
          u,
          !1
        );
        return;
      case "select":
        W("invalid", l), e = i = n = null;
        for (u in a)
          if (a.hasOwnProperty(u) && (c = a[u], c != null))
            switch (u) {
              case "value":
                n = c;
                break;
              case "defaultValue":
                i = c;
                break;
              case "multiple":
                e = c;
              default:
                ml(l, t, u, c, a, null);
            }
        t = n, a = i, l.multiple = !!e, t != null ? ue(l, !!e, t, !1) : a != null && ue(l, !!e, a, !0);
        return;
      case "textarea":
        W("invalid", l), n = u = e = null;
        for (i in a)
          if (a.hasOwnProperty(i) && (c = a[i], c != null))
            switch (i) {
              case "value":
                e = c;
                break;
              case "defaultValue":
                u = c;
                break;
              case "children":
                n = c;
                break;
              case "dangerouslySetInnerHTML":
                if (c != null) throw Error(g(91));
                break;
              default:
                ml(l, t, i, c, a, null);
            }
        Zf(l, e, u, n);
        return;
      case "option":
        for (s in a)
          a.hasOwnProperty(s) && (e = a[s], e != null) && (s === "selected" ? l.selected = e && typeof e != "function" && typeof e != "symbol" : ml(l, t, s, e, a, null));
        return;
      case "dialog":
        W("beforetoggle", l), W("toggle", l), W("cancel", l), W("close", l);
        break;
      case "iframe":
      case "object":
        W("load", l);
        break;
      case "video":
      case "audio":
        for (e = 0; e < Eu.length; e++)
          W(Eu[e], l);
        break;
      case "image":
        W("error", l), W("load", l);
        break;
      case "details":
        W("toggle", l);
        break;
      case "embed":
      case "source":
      case "link":
        W("error", l), W("load", l);
      case "area":
      case "base":
      case "br":
      case "col":
      case "hr":
      case "keygen":
      case "meta":
      case "param":
      case "track":
      case "wbr":
      case "menuitem":
        for (y in a)
          if (a.hasOwnProperty(y) && (e = a[y], e != null))
            switch (y) {
              case "children":
              case "dangerouslySetInnerHTML":
                throw Error(g(137, t));
              default:
                ml(l, t, y, e, a, null);
            }
        return;
      default:
        if (ci(t)) {
          for (z in a)
            a.hasOwnProperty(z) && (e = a[z], e !== void 0 && wc(
              l,
              t,
              z,
              e,
              a,
              void 0
            ));
          return;
        }
    }
    for (c in a)
      a.hasOwnProperty(c) && (e = a[c], e != null && ml(l, t, c, e, a, null));
  }
  function Av(l, t, a, e) {
    switch (t) {
      case "div":
      case "span":
      case "svg":
      case "path":
      case "a":
      case "g":
      case "p":
      case "li":
        break;
      case "input":
        var u = null, n = null, i = null, c = null, s = null, y = null, z = null;
        for (S in a) {
          var T = a[S];
          if (a.hasOwnProperty(S) && T != null)
            switch (S) {
              case "checked":
                break;
              case "value":
                break;
              case "defaultValue":
                s = T;
              default:
                e.hasOwnProperty(S) || ml(l, t, S, null, e, T);
            }
        }
        for (var r in e) {
          var S = e[r];
          if (T = a[r], e.hasOwnProperty(r) && (S != null || T != null))
            switch (r) {
              case "type":
                n = S;
                break;
              case "name":
                u = S;
                break;
              case "checked":
                y = S;
                break;
              case "defaultChecked":
                z = S;
                break;
              case "value":
                i = S;
                break;
              case "defaultValue":
                c = S;
                break;
              case "children":
              case "dangerouslySetInnerHTML":
                if (S != null)
                  throw Error(g(137, t));
                break;
              default:
                S !== T && ml(
                  l,
                  t,
                  r,
                  S,
                  e,
                  T
                );
            }
        }
        ni(
          l,
          i,
          c,
          s,
          y,
          z,
          n,
          u
        );
        return;
      case "select":
        S = i = c = r = null;
        for (n in a)
          if (s = a[n], a.hasOwnProperty(n) && s != null)
            switch (n) {
              case "value":
                break;
              case "multiple":
                S = s;
              default:
                e.hasOwnProperty(n) || ml(
                  l,
                  t,
                  n,
                  null,
                  e,
                  s
                );
            }
        for (u in e)
          if (n = e[u], s = a[u], e.hasOwnProperty(u) && (n != null || s != null))
            switch (u) {
              case "value":
                r = n;
                break;
              case "defaultValue":
                c = n;
                break;
              case "multiple":
                i = n;
              default:
                n !== s && ml(
                  l,
                  t,
                  u,
                  n,
                  e,
                  s
                );
            }
        t = c, a = i, e = S, r != null ? ue(l, !!a, r, !1) : !!e != !!a && (t != null ? ue(l, !!a, t, !0) : ue(l, !!a, a ? [] : "", !1));
        return;
      case "textarea":
        S = r = null;
        for (c in a)
          if (u = a[c], a.hasOwnProperty(c) && u != null && !e.hasOwnProperty(c))
            switch (c) {
              case "value":
                break;
              case "children":
                break;
              default:
                ml(l, t, c, null, e, u);
            }
        for (i in e)
          if (u = e[i], n = a[i], e.hasOwnProperty(i) && (u != null || n != null))
            switch (i) {
              case "value":
                r = u;
                break;
              case "defaultValue":
                S = u;
                break;
              case "children":
                break;
              case "dangerouslySetInnerHTML":
                if (u != null) throw Error(g(91));
                break;
              default:
                u !== n && ml(l, t, i, u, e, n);
            }
        Xf(l, r, S);
        return;
      case "option":
        for (var D in a)
          r = a[D], a.hasOwnProperty(D) && r != null && !e.hasOwnProperty(D) && (D === "selected" ? l.selected = !1 : ml(
            l,
            t,
            D,
            null,
            e,
            r
          ));
        for (s in e)
          r = e[s], S = a[s], e.hasOwnProperty(s) && r !== S && (r != null || S != null) && (s === "selected" ? l.selected = r && typeof r != "function" && typeof r != "symbol" : ml(
            l,
            t,
            s,
            r,
            e,
            S
          ));
        return;
      case "img":
      case "link":
      case "area":
      case "base":
      case "br":
      case "col":
      case "embed":
      case "hr":
      case "keygen":
      case "meta":
      case "param":
      case "source":
      case "track":
      case "wbr":
      case "menuitem":
        for (var B in a)
          r = a[B], a.hasOwnProperty(B) && r != null && !e.hasOwnProperty(B) && ml(l, t, B, null, e, r);
        for (y in e)
          if (r = e[y], S = a[y], e.hasOwnProperty(y) && r !== S && (r != null || S != null))
            switch (y) {
              case "children":
              case "dangerouslySetInnerHTML":
                if (r != null)
                  throw Error(g(137, t));
                break;
              default:
                ml(
                  l,
                  t,
                  y,
                  r,
                  e,
                  S
                );
            }
        return;
      default:
        if (ci(t)) {
          for (var hl in a)
            r = a[hl], a.hasOwnProperty(hl) && r !== void 0 && !e.hasOwnProperty(hl) && wc(
              l,
              t,
              hl,
              void 0,
              e,
              r
            );
          for (z in e)
            r = e[z], S = a[z], !e.hasOwnProperty(z) || r === S || r === void 0 && S === void 0 || wc(
              l,
              t,
              z,
              r,
              e,
              S
            );
          return;
        }
    }
    for (var h in a)
      r = a[h], a.hasOwnProperty(h) && r != null && !e.hasOwnProperty(h) && ml(l, t, h, null, e, r);
    for (T in e)
      r = e[T], S = a[T], !e.hasOwnProperty(T) || r === S || r == null && S == null || ml(l, t, T, r, e, S);
  }
  function Ko(l) {
    switch (l) {
      case "css":
      case "script":
      case "font":
      case "img":
      case "image":
      case "input":
      case "link":
        return !0;
      default:
        return !1;
    }
  }
  function Tv() {
    if (typeof performance.getEntriesByType == "function") {
      for (var l = 0, t = 0, a = performance.getEntriesByType("resource"), e = 0; e < a.length; e++) {
        var u = a[e], n = u.transferSize, i = u.initiatorType, c = u.duration;
        if (n && c && Ko(i)) {
          for (i = 0, c = u.responseEnd, e += 1; e < a.length; e++) {
            var s = a[e], y = s.startTime;
            if (y > c) break;
            var z = s.transferSize, T = s.initiatorType;
            z && Ko(T) && (s = s.responseEnd, i += z * (s < c ? 1 : (c - y) / (s - y)));
          }
          if (--e, t += 8 * (n + i) / (u.duration / 1e3), l++, 10 < l) break;
        }
      }
      if (0 < l) return t / l / 1e6;
    }
    return navigator.connection && (l = navigator.connection.downlink, typeof l == "number") ? l : 5;
  }
  var $c = null, Wc = null;
  function Bn(l) {
    return l.nodeType === 9 ? l : l.ownerDocument;
  }
  function Jo(l) {
    switch (l) {
      case "http://www.w3.org/2000/svg":
        return 1;
      case "http://www.w3.org/1998/Math/MathML":
        return 2;
      default:
        return 0;
    }
  }
  function wo(l, t) {
    if (l === 0)
      switch (t) {
        case "svg":
          return 1;
        case "math":
          return 2;
        default:
          return 0;
      }
    return l === 1 && t === "foreignObject" ? 0 : l;
  }
  function kc(l, t) {
    return l === "textarea" || l === "noscript" || typeof t.children == "string" || typeof t.children == "number" || typeof t.children == "bigint" || typeof t.dangerouslySetInnerHTML == "object" && t.dangerouslySetInnerHTML !== null && t.dangerouslySetInnerHTML.__html != null;
  }
  var Fc = null;
  function xv() {
    var l = window.event;
    return l && l.type === "popstate" ? l === Fc ? !1 : (Fc = l, !0) : (Fc = null, !1);
  }
  var $o = typeof setTimeout == "function" ? setTimeout : void 0, jv = typeof clearTimeout == "function" ? clearTimeout : void 0, Wo = typeof Promise == "function" ? Promise : void 0, _v = typeof queueMicrotask == "function" ? queueMicrotask : typeof Wo < "u" ? function(l) {
    return Wo.resolve(null).then(l).catch(Nv);
  } : $o;
  function Nv(l) {
    setTimeout(function() {
      throw l;
    });
  }
  function xa(l) {
    return l === "head";
  }
  function ko(l, t) {
    var a = t, e = 0;
    do {
      var u = a.nextSibling;
      if (l.removeChild(a), u && u.nodeType === 8)
        if (a = u.data, a === "/$" || a === "/&") {
          if (e === 0) {
            l.removeChild(u), qe(t);
            return;
          }
          e--;
        } else if (a === "$" || a === "$?" || a === "$~" || a === "$!" || a === "&")
          e++;
        else if (a === "html")
          Tu(l.ownerDocument.documentElement);
        else if (a === "head") {
          a = l.ownerDocument.head, Tu(a);
          for (var n = a.firstChild; n; ) {
            var i = n.nextSibling, c = n.nodeName;
            n[Qe] || c === "SCRIPT" || c === "STYLE" || c === "LINK" && n.rel.toLowerCase() === "stylesheet" || a.removeChild(n), n = i;
          }
        } else
          a === "body" && Tu(l.ownerDocument.body);
      a = u;
    } while (a);
    qe(t);
  }
  function Fo(l, t) {
    var a = l;
    l = 0;
    do {
      var e = a.nextSibling;
      if (a.nodeType === 1 ? t ? (a._stashedDisplay = a.style.display, a.style.display = "none") : (a.style.display = a._stashedDisplay || "", a.getAttribute("style") === "" && a.removeAttribute("style")) : a.nodeType === 3 && (t ? (a._stashedText = a.nodeValue, a.nodeValue = "") : a.nodeValue = a._stashedText || ""), e && e.nodeType === 8)
        if (a = e.data, a === "/$") {
          if (l === 0) break;
          l--;
        } else
          a !== "$" && a !== "$?" && a !== "$~" && a !== "$!" || l++;
      a = e;
    } while (a);
  }
  function Ic(l) {
    var t = l.firstChild;
    for (t && t.nodeType === 10 && (t = t.nextSibling); t; ) {
      var a = t;
      switch (t = t.nextSibling, a.nodeName) {
        case "HTML":
        case "HEAD":
        case "BODY":
          Ic(a), ei(a);
          continue;
        case "SCRIPT":
        case "STYLE":
          continue;
        case "LINK":
          if (a.rel.toLowerCase() === "stylesheet") continue;
      }
      l.removeChild(a);
    }
  }
  function Mv(l, t, a, e) {
    for (; l.nodeType === 1; ) {
      var u = a;
      if (l.nodeName.toLowerCase() !== t.toLowerCase()) {
        if (!e && (l.nodeName !== "INPUT" || l.type !== "hidden"))
          break;
      } else if (e) {
        if (!l[Qe])
          switch (t) {
            case "meta":
              if (!l.hasAttribute("itemprop")) break;
              return l;
            case "link":
              if (n = l.getAttribute("rel"), n === "stylesheet" && l.hasAttribute("data-precedence"))
                break;
              if (n !== u.rel || l.getAttribute("href") !== (u.href == null || u.href === "" ? null : u.href) || l.getAttribute("crossorigin") !== (u.crossOrigin == null ? null : u.crossOrigin) || l.getAttribute("title") !== (u.title == null ? null : u.title))
                break;
              return l;
            case "style":
              if (l.hasAttribute("data-precedence")) break;
              return l;
            case "script":
              if (n = l.getAttribute("src"), (n !== (u.src == null ? null : u.src) || l.getAttribute("type") !== (u.type == null ? null : u.type) || l.getAttribute("crossorigin") !== (u.crossOrigin == null ? null : u.crossOrigin)) && n && l.hasAttribute("async") && !l.hasAttribute("itemprop"))
                break;
              return l;
            default:
              return l;
          }
      } else if (t === "input" && l.type === "hidden") {
        var n = u.name == null ? null : "" + u.name;
        if (u.type === "hidden" && l.getAttribute("name") === n)
          return l;
      } else return l;
      if (l = Nt(l.nextSibling), l === null) break;
    }
    return null;
  }
  function Ov(l, t, a) {
    if (t === "") return null;
    for (; l.nodeType !== 3; )
      if ((l.nodeType !== 1 || l.nodeName !== "INPUT" || l.type !== "hidden") && !a || (l = Nt(l.nextSibling), l === null)) return null;
    return l;
  }
  function Io(l, t) {
    for (; l.nodeType !== 8; )
      if ((l.nodeType !== 1 || l.nodeName !== "INPUT" || l.type !== "hidden") && !t || (l = Nt(l.nextSibling), l === null)) return null;
    return l;
  }
  function Pc(l) {
    return l.data === "$?" || l.data === "$~";
  }
  function lf(l) {
    return l.data === "$!" || l.data === "$?" && l.ownerDocument.readyState !== "loading";
  }
  function Dv(l, t) {
    var a = l.ownerDocument;
    if (l.data === "$~") l._reactRetry = t;
    else if (l.data !== "$?" || a.readyState !== "loading")
      t();
    else {
      var e = function() {
        t(), a.removeEventListener("DOMContentLoaded", e);
      };
      a.addEventListener("DOMContentLoaded", e), l._reactRetry = e;
    }
  }
  function Nt(l) {
    for (; l != null; l = l.nextSibling) {
      var t = l.nodeType;
      if (t === 1 || t === 3) break;
      if (t === 8) {
        if (t = l.data, t === "$" || t === "$!" || t === "$?" || t === "$~" || t === "&" || t === "F!" || t === "F")
          break;
        if (t === "/$" || t === "/&") return null;
      }
    }
    return l;
  }
  var tf = null;
  function Po(l) {
    l = l.nextSibling;
    for (var t = 0; l; ) {
      if (l.nodeType === 8) {
        var a = l.data;
        if (a === "/$" || a === "/&") {
          if (t === 0)
            return Nt(l.nextSibling);
          t--;
        } else
          a !== "$" && a !== "$!" && a !== "$?" && a !== "$~" && a !== "&" || t++;
      }
      l = l.nextSibling;
    }
    return null;
  }
  function lm(l) {
    l = l.previousSibling;
    for (var t = 0; l; ) {
      if (l.nodeType === 8) {
        var a = l.data;
        if (a === "$" || a === "$!" || a === "$?" || a === "$~" || a === "&") {
          if (t === 0) return l;
          t--;
        } else a !== "/$" && a !== "/&" || t++;
      }
      l = l.previousSibling;
    }
    return null;
  }
  function tm(l, t, a) {
    switch (t = Bn(a), l) {
      case "html":
        if (l = t.documentElement, !l) throw Error(g(452));
        return l;
      case "head":
        if (l = t.head, !l) throw Error(g(453));
        return l;
      case "body":
        if (l = t.body, !l) throw Error(g(454));
        return l;
      default:
        throw Error(g(451));
    }
  }
  function Tu(l) {
    for (var t = l.attributes; t.length; )
      l.removeAttributeNode(t[0]);
    ei(l);
  }
  var Mt = /* @__PURE__ */ new Map(), am = /* @__PURE__ */ new Set();
  function Gn(l) {
    return typeof l.getRootNode == "function" ? l.getRootNode() : l.nodeType === 9 ? l : l.ownerDocument;
  }
  var na = _.d;
  _.d = {
    f: Uv,
    r: Hv,
    D: Cv,
    C: Rv,
    L: qv,
    m: Yv,
    X: Gv,
    S: Bv,
    M: Xv
  };
  function Uv() {
    var l = na.f(), t = On();
    return l || t;
  }
  function Hv(l) {
    var t = te(l);
    t !== null && t.tag === 5 && t.type === "form" ? Sd(t) : na.r(l);
  }
  var He = typeof document > "u" ? null : document;
  function em(l, t, a) {
    var e = He;
    if (e && typeof t == "string" && t) {
      var u = zt(t);
      u = 'link[rel="' + l + '"][href="' + u + '"]', typeof a == "string" && (u += '[crossorigin="' + a + '"]'), am.has(u) || (am.add(u), l = { rel: l, crossOrigin: a, href: t }, e.querySelector(u) === null && (t = e.createElement("link"), $l(t, "link", l), Gl(t), e.head.appendChild(t)));
    }
  }
  function Cv(l) {
    na.D(l), em("dns-prefetch", l, null);
  }
  function Rv(l, t) {
    na.C(l, t), em("preconnect", l, t);
  }
  function qv(l, t, a) {
    na.L(l, t, a);
    var e = He;
    if (e && l && t) {
      var u = 'link[rel="preload"][as="' + zt(t) + '"]';
      t === "image" && a && a.imageSrcSet ? (u += '[imagesrcset="' + zt(
        a.imageSrcSet
      ) + '"]', typeof a.imageSizes == "string" && (u += '[imagesizes="' + zt(
        a.imageSizes
      ) + '"]')) : u += '[href="' + zt(l) + '"]';
      var n = u;
      switch (t) {
        case "style":
          n = Ce(l);
          break;
        case "script":
          n = Re(l);
      }
      Mt.has(n) || (l = C(
        {
          rel: "preload",
          href: t === "image" && a && a.imageSrcSet ? void 0 : l,
          as: t
        },
        a
      ), Mt.set(n, l), e.querySelector(u) !== null || t === "style" && e.querySelector(xu(n)) || t === "script" && e.querySelector(ju(n)) || (t = e.createElement("link"), $l(t, "link", l), Gl(t), e.head.appendChild(t)));
    }
  }
  function Yv(l, t) {
    na.m(l, t);
    var a = He;
    if (a && l) {
      var e = t && typeof t.as == "string" ? t.as : "script", u = 'link[rel="modulepreload"][as="' + zt(e) + '"][href="' + zt(l) + '"]', n = u;
      switch (e) {
        case "audioworklet":
        case "paintworklet":
        case "serviceworker":
        case "sharedworker":
        case "worker":
        case "script":
          n = Re(l);
      }
      if (!Mt.has(n) && (l = C({ rel: "modulepreload", href: l }, t), Mt.set(n, l), a.querySelector(u) === null)) {
        switch (e) {
          case "audioworklet":
          case "paintworklet":
          case "serviceworker":
          case "sharedworker":
          case "worker":
          case "script":
            if (a.querySelector(ju(n)))
              return;
        }
        e = a.createElement("link"), $l(e, "link", l), Gl(e), a.head.appendChild(e);
      }
    }
  }
  function Bv(l, t, a) {
    na.S(l, t, a);
    var e = He;
    if (e && l) {
      var u = ae(e).hoistableStyles, n = Ce(l);
      t = t || "default";
      var i = u.get(n);
      if (!i) {
        var c = { loading: 0, preload: null };
        if (i = e.querySelector(
          xu(n)
        ))
          c.loading = 5;
        else {
          l = C(
            { rel: "stylesheet", href: l, "data-precedence": t },
            a
          ), (a = Mt.get(n)) && af(l, a);
          var s = i = e.createElement("link");
          Gl(s), $l(s, "link", l), s._p = new Promise(function(y, z) {
            s.onload = y, s.onerror = z;
          }), s.addEventListener("load", function() {
            c.loading |= 1;
          }), s.addEventListener("error", function() {
            c.loading |= 2;
          }), c.loading |= 4, Xn(i, t, e);
        }
        i = {
          type: "stylesheet",
          instance: i,
          count: 1,
          state: c
        }, u.set(n, i);
      }
    }
  }
  function Gv(l, t) {
    na.X(l, t);
    var a = He;
    if (a && l) {
      var e = ae(a).hoistableScripts, u = Re(l), n = e.get(u);
      n || (n = a.querySelector(ju(u)), n || (l = C({ src: l, async: !0 }, t), (t = Mt.get(u)) && ef(l, t), n = a.createElement("script"), Gl(n), $l(n, "link", l), a.head.appendChild(n)), n = {
        type: "script",
        instance: n,
        count: 1,
        state: null
      }, e.set(u, n));
    }
  }
  function Xv(l, t) {
    na.M(l, t);
    var a = He;
    if (a && l) {
      var e = ae(a).hoistableScripts, u = Re(l), n = e.get(u);
      n || (n = a.querySelector(ju(u)), n || (l = C({ src: l, async: !0, type: "module" }, t), (t = Mt.get(u)) && ef(l, t), n = a.createElement("script"), Gl(n), $l(n, "link", l), a.head.appendChild(n)), n = {
        type: "script",
        instance: n,
        count: 1,
        state: null
      }, e.set(u, n));
    }
  }
  function um(l, t, a, e) {
    var u = (u = J.current) ? Gn(u) : null;
    if (!u) throw Error(g(446));
    switch (l) {
      case "meta":
      case "title":
        return null;
      case "style":
        return typeof a.precedence == "string" && typeof a.href == "string" ? (t = Ce(a.href), a = ae(
          u
        ).hoistableStyles, e = a.get(t), e || (e = {
          type: "style",
          instance: null,
          count: 0,
          state: null
        }, a.set(t, e)), e) : { type: "void", instance: null, count: 0, state: null };
      case "link":
        if (a.rel === "stylesheet" && typeof a.href == "string" && typeof a.precedence == "string") {
          l = Ce(a.href);
          var n = ae(
            u
          ).hoistableStyles, i = n.get(l);
          if (i || (u = u.ownerDocument || u, i = {
            type: "stylesheet",
            instance: null,
            count: 0,
            state: { loading: 0, preload: null }
          }, n.set(l, i), (n = u.querySelector(
            xu(l)
          )) && !n._p && (i.instance = n, i.state.loading = 5), Mt.has(l) || (a = {
            rel: "preload",
            as: "style",
            href: a.href,
            crossOrigin: a.crossOrigin,
            integrity: a.integrity,
            media: a.media,
            hrefLang: a.hrefLang,
            referrerPolicy: a.referrerPolicy
          }, Mt.set(l, a), n || Zv(
            u,
            l,
            a,
            i.state
          ))), t && e === null)
            throw Error(g(528, ""));
          return i;
        }
        if (t && e !== null)
          throw Error(g(529, ""));
        return null;
      case "script":
        return t = a.async, a = a.src, typeof a == "string" && t && typeof t != "function" && typeof t != "symbol" ? (t = Re(a), a = ae(
          u
        ).hoistableScripts, e = a.get(t), e || (e = {
          type: "script",
          instance: null,
          count: 0,
          state: null
        }, a.set(t, e)), e) : { type: "void", instance: null, count: 0, state: null };
      default:
        throw Error(g(444, l));
    }
  }
  function Ce(l) {
    return 'href="' + zt(l) + '"';
  }
  function xu(l) {
    return 'link[rel="stylesheet"][' + l + "]";
  }
  function nm(l) {
    return C({}, l, {
      "data-precedence": l.precedence,
      precedence: null
    });
  }
  function Zv(l, t, a, e) {
    l.querySelector('link[rel="preload"][as="style"][' + t + "]") ? e.loading = 1 : (t = l.createElement("link"), e.preload = t, t.addEventListener("load", function() {
      return e.loading |= 1;
    }), t.addEventListener("error", function() {
      return e.loading |= 2;
    }), $l(t, "link", a), Gl(t), l.head.appendChild(t));
  }
  function Re(l) {
    return '[src="' + zt(l) + '"]';
  }
  function ju(l) {
    return "script[async]" + l;
  }
  function im(l, t, a) {
    if (t.count++, t.instance === null)
      switch (t.type) {
        case "style":
          var e = l.querySelector(
            'style[data-href~="' + zt(a.href) + '"]'
          );
          if (e)
            return t.instance = e, Gl(e), e;
          var u = C({}, a, {
            "data-href": a.href,
            "data-precedence": a.precedence,
            href: null,
            precedence: null
          });
          return e = (l.ownerDocument || l).createElement(
            "style"
          ), Gl(e), $l(e, "style", u), Xn(e, a.precedence, l), t.instance = e;
        case "stylesheet":
          u = Ce(a.href);
          var n = l.querySelector(
            xu(u)
          );
          if (n)
            return t.state.loading |= 4, t.instance = n, Gl(n), n;
          e = nm(a), (u = Mt.get(u)) && af(e, u), n = (l.ownerDocument || l).createElement("link"), Gl(n);
          var i = n;
          return i._p = new Promise(function(c, s) {
            i.onload = c, i.onerror = s;
          }), $l(n, "link", e), t.state.loading |= 4, Xn(n, a.precedence, l), t.instance = n;
        case "script":
          return n = Re(a.src), (u = l.querySelector(
            ju(n)
          )) ? (t.instance = u, Gl(u), u) : (e = a, (u = Mt.get(n)) && (e = C({}, a), ef(e, u)), l = l.ownerDocument || l, u = l.createElement("script"), Gl(u), $l(u, "link", e), l.head.appendChild(u), t.instance = u);
        case "void":
          return null;
        default:
          throw Error(g(443, t.type));
      }
    else
      t.type === "stylesheet" && (t.state.loading & 4) === 0 && (e = t.instance, t.state.loading |= 4, Xn(e, a.precedence, l));
    return t.instance;
  }
  function Xn(l, t, a) {
    for (var e = a.querySelectorAll(
      'link[rel="stylesheet"][data-precedence],style[data-precedence]'
    ), u = e.length ? e[e.length - 1] : null, n = u, i = 0; i < e.length; i++) {
      var c = e[i];
      if (c.dataset.precedence === t) n = c;
      else if (n !== u) break;
    }
    n ? n.parentNode.insertBefore(l, n.nextSibling) : (t = a.nodeType === 9 ? a.head : a, t.insertBefore(l, t.firstChild));
  }
  function af(l, t) {
    l.crossOrigin == null && (l.crossOrigin = t.crossOrigin), l.referrerPolicy == null && (l.referrerPolicy = t.referrerPolicy), l.title == null && (l.title = t.title);
  }
  function ef(l, t) {
    l.crossOrigin == null && (l.crossOrigin = t.crossOrigin), l.referrerPolicy == null && (l.referrerPolicy = t.referrerPolicy), l.integrity == null && (l.integrity = t.integrity);
  }
  var Zn = null;
  function cm(l, t, a) {
    if (Zn === null) {
      var e = /* @__PURE__ */ new Map(), u = Zn = /* @__PURE__ */ new Map();
      u.set(a, e);
    } else
      u = Zn, e = u.get(a), e || (e = /* @__PURE__ */ new Map(), u.set(a, e));
    if (e.has(l)) return e;
    for (e.set(l, null), a = a.getElementsByTagName(l), u = 0; u < a.length; u++) {
      var n = a[u];
      if (!(n[Qe] || n[Vl] || l === "link" && n.getAttribute("rel") === "stylesheet") && n.namespaceURI !== "http://www.w3.org/2000/svg") {
        var i = n.getAttribute(t) || "";
        i = l + i;
        var c = e.get(i);
        c ? c.push(n) : e.set(i, [n]);
      }
    }
    return e;
  }
  function fm(l, t, a) {
    l = l.ownerDocument || l, l.head.insertBefore(
      a,
      t === "title" ? l.querySelector("head > title") : null
    );
  }
  function Qv(l, t, a) {
    if (a === 1 || t.itemProp != null) return !1;
    switch (l) {
      case "meta":
      case "title":
        return !0;
      case "style":
        if (typeof t.precedence != "string" || typeof t.href != "string" || t.href === "")
          break;
        return !0;
      case "link":
        if (typeof t.rel != "string" || typeof t.href != "string" || t.href === "" || t.onLoad || t.onError)
          break;
        return t.rel === "stylesheet" ? (l = t.disabled, typeof t.precedence == "string" && l == null) : !0;
      case "script":
        if (t.async && typeof t.async != "function" && typeof t.async != "symbol" && !t.onLoad && !t.onError && t.src && typeof t.src == "string")
          return !0;
    }
    return !1;
  }
  function sm(l) {
    return !(l.type === "stylesheet" && (l.state.loading & 3) === 0);
  }
  function Lv(l, t, a, e) {
    if (a.type === "stylesheet" && (typeof e.media != "string" || matchMedia(e.media).matches !== !1) && (a.state.loading & 4) === 0) {
      if (a.instance === null) {
        var u = Ce(e.href), n = t.querySelector(
          xu(u)
        );
        if (n) {
          t = n._p, t !== null && typeof t == "object" && typeof t.then == "function" && (l.count++, l = Qn.bind(l), t.then(l, l)), a.state.loading |= 4, a.instance = n, Gl(n);
          return;
        }
        n = t.ownerDocument || t, e = nm(e), (u = Mt.get(u)) && af(e, u), n = n.createElement("link"), Gl(n);
        var i = n;
        i._p = new Promise(function(c, s) {
          i.onload = c, i.onerror = s;
        }), $l(n, "link", e), a.instance = n;
      }
      l.stylesheets === null && (l.stylesheets = /* @__PURE__ */ new Map()), l.stylesheets.set(a, t), (t = a.state.preload) && (a.state.loading & 3) === 0 && (l.count++, a = Qn.bind(l), t.addEventListener("load", a), t.addEventListener("error", a));
    }
  }
  var uf = 0;
  function Vv(l, t) {
    return l.stylesheets && l.count === 0 && Vn(l, l.stylesheets), 0 < l.count || 0 < l.imgCount ? function(a) {
      var e = setTimeout(function() {
        if (l.stylesheets && Vn(l, l.stylesheets), l.unsuspend) {
          var n = l.unsuspend;
          l.unsuspend = null, n();
        }
      }, 6e4 + t);
      0 < l.imgBytes && uf === 0 && (uf = 62500 * Tv());
      var u = setTimeout(
        function() {
          if (l.waitingForImages = !1, l.count === 0 && (l.stylesheets && Vn(l, l.stylesheets), l.unsuspend)) {
            var n = l.unsuspend;
            l.unsuspend = null, n();
          }
        },
        (l.imgBytes > uf ? 50 : 800) + t
      );
      return l.unsuspend = a, function() {
        l.unsuspend = null, clearTimeout(e), clearTimeout(u);
      };
    } : null;
  }
  function Qn() {
    if (this.count--, this.count === 0 && (this.imgCount === 0 || !this.waitingForImages)) {
      if (this.stylesheets) Vn(this, this.stylesheets);
      else if (this.unsuspend) {
        var l = this.unsuspend;
        this.unsuspend = null, l();
      }
    }
  }
  var Ln = null;
  function Vn(l, t) {
    l.stylesheets = null, l.unsuspend !== null && (l.count++, Ln = /* @__PURE__ */ new Map(), t.forEach(Kv, l), Ln = null, Qn.call(l));
  }
  function Kv(l, t) {
    if (!(t.state.loading & 4)) {
      var a = Ln.get(l);
      if (a) var e = a.get(null);
      else {
        a = /* @__PURE__ */ new Map(), Ln.set(l, a);
        for (var u = l.querySelectorAll(
          "link[data-precedence],style[data-precedence]"
        ), n = 0; n < u.length; n++) {
          var i = u[n];
          (i.nodeName === "LINK" || i.getAttribute("media") !== "not all") && (a.set(i.dataset.precedence, i), e = i);
        }
        e && a.set(null, e);
      }
      u = t.instance, i = u.getAttribute("data-precedence"), n = a.get(i) || e, n === e && a.set(null, u), a.set(i, u), this.count++, e = Qn.bind(this), u.addEventListener("load", e), u.addEventListener("error", e), n ? n.parentNode.insertBefore(u, n.nextSibling) : (l = l.nodeType === 9 ? l.head : l, l.insertBefore(u, l.firstChild)), t.state.loading |= 4;
    }
  }
  var _u = {
    $$typeof: Bl,
    Provider: null,
    Consumer: null,
    _currentValue: Y,
    _currentValue2: Y,
    _threadCount: 0
  };
  function Jv(l, t, a, e, u, n, i, c, s) {
    this.tag = 1, this.containerInfo = l, this.pingCache = this.current = this.pendingChildren = null, this.timeoutHandle = -1, this.callbackNode = this.next = this.pendingContext = this.context = this.cancelPendingCommit = null, this.callbackPriority = 0, this.expirationTimes = Pn(-1), this.entangledLanes = this.shellSuspendCounter = this.errorRecoveryDisabledLanes = this.expiredLanes = this.warmLanes = this.pingedLanes = this.suspendedLanes = this.pendingLanes = 0, this.entanglements = Pn(0), this.hiddenUpdates = Pn(null), this.identifierPrefix = e, this.onUncaughtError = u, this.onCaughtError = n, this.onRecoverableError = i, this.pooledCache = null, this.pooledCacheLanes = 0, this.formState = s, this.incompleteTransitions = /* @__PURE__ */ new Map();
  }
  function dm(l, t, a, e, u, n, i, c, s, y, z, T) {
    return l = new Jv(
      l,
      t,
      a,
      i,
      s,
      y,
      z,
      T,
      c
    ), t = 1, n === !0 && (t |= 24), n = dt(3, null, null, t), l.current = n, n.stateNode = l, t = Yi(), t.refCount++, l.pooledCache = t, t.refCount++, n.memoizedState = {
      element: e,
      isDehydrated: a,
      cache: t
    }, Zi(n), l;
  }
  function om(l) {
    return l ? (l = me, l) : me;
  }
  function mm(l, t, a, e, u, n) {
    u = om(u), e.context === null ? e.context = u : e.pendingContext = u, e = va(t), e.payload = { element: a }, n = n === void 0 ? null : n, n !== null && (e.callback = n), a = ya(l, e, t), a !== null && (it(a, l, t), nu(a, l, t));
  }
  function hm(l, t) {
    if (l = l.memoizedState, l !== null && l.dehydrated !== null) {
      var a = l.retryLane;
      l.retryLane = a !== 0 && a < t ? a : t;
    }
  }
  function nf(l, t) {
    hm(l, t), (l = l.alternate) && hm(l, t);
  }
  function vm(l) {
    if (l.tag === 13 || l.tag === 31) {
      var t = Ga(l, 67108864);
      t !== null && it(t, l, 67108864), nf(l, 67108864);
    }
  }
  function ym(l) {
    if (l.tag === 13 || l.tag === 31) {
      var t = yt();
      t = li(t);
      var a = Ga(l, t);
      a !== null && it(a, l, t), nf(l, t);
    }
  }
  var Kn = !0;
  function wv(l, t, a, e) {
    var u = b.T;
    b.T = null;
    var n = _.p;
    try {
      _.p = 2, cf(l, t, a, e);
    } finally {
      _.p = n, b.T = u;
    }
  }
  function $v(l, t, a, e) {
    var u = b.T;
    b.T = null;
    var n = _.p;
    try {
      _.p = 8, cf(l, t, a, e);
    } finally {
      _.p = n, b.T = u;
    }
  }
  function cf(l, t, a, e) {
    if (Kn) {
      var u = ff(e);
      if (u === null)
        Jc(
          l,
          t,
          e,
          Jn,
          a
        ), gm(l, e);
      else if (kv(
        u,
        l,
        t,
        a,
        e
      ))
        e.stopPropagation();
      else if (gm(l, e), t & 4 && -1 < Wv.indexOf(l)) {
        for (; u !== null; ) {
          var n = te(u);
          if (n !== null)
            switch (n.tag) {
              case 3:
                if (n = n.stateNode, n.current.memoizedState.isDehydrated) {
                  var i = Ca(n.pendingLanes);
                  if (i !== 0) {
                    var c = n;
                    for (c.pendingLanes |= 2, c.entangledLanes |= 2; i; ) {
                      var s = 1 << 31 - ft(i);
                      c.entanglements[1] |= s, i &= ~s;
                    }
                    Xt(n), (il & 6) === 0 && (Nn = Fl() + 500, zu(0));
                  }
                }
                break;
              case 31:
              case 13:
                c = Ga(n, 2), c !== null && it(c, n, 2), On(), nf(n, 2);
            }
          if (n = ff(e), n === null && Jc(
            l,
            t,
            e,
            Jn,
            a
          ), n === u) break;
          u = n;
        }
        u !== null && e.stopPropagation();
      } else
        Jc(
          l,
          t,
          e,
          null,
          a
        );
    }
  }
  function ff(l) {
    return l = si(l), sf(l);
  }
  var Jn = null;
  function sf(l) {
    if (Jn = null, l = le(l), l !== null) {
      var t = G(l);
      if (t === null) l = null;
      else {
        var a = t.tag;
        if (a === 13) {
          if (l = Al(t), l !== null) return l;
          l = null;
        } else if (a === 31) {
          if (l = nl(t), l !== null) return l;
          l = null;
        } else if (a === 3) {
          if (t.stateNode.current.memoizedState.isDehydrated)
            return t.tag === 3 ? t.stateNode.containerInfo : null;
          l = null;
        } else t !== l && (l = null);
      }
    }
    return Jn = l, null;
  }
  function rm(l) {
    switch (l) {
      case "beforetoggle":
      case "cancel":
      case "click":
      case "close":
      case "contextmenu":
      case "copy":
      case "cut":
      case "auxclick":
      case "dblclick":
      case "dragend":
      case "dragstart":
      case "drop":
      case "focusin":
      case "focusout":
      case "input":
      case "invalid":
      case "keydown":
      case "keypress":
      case "keyup":
      case "mousedown":
      case "mouseup":
      case "paste":
      case "pause":
      case "play":
      case "pointercancel":
      case "pointerdown":
      case "pointerup":
      case "ratechange":
      case "reset":
      case "resize":
      case "seeked":
      case "submit":
      case "toggle":
      case "touchcancel":
      case "touchend":
      case "touchstart":
      case "volumechange":
      case "change":
      case "selectionchange":
      case "textInput":
      case "compositionstart":
      case "compositionend":
      case "compositionupdate":
      case "beforeblur":
      case "afterblur":
      case "beforeinput":
      case "blur":
      case "fullscreenchange":
      case "focus":
      case "hashchange":
      case "popstate":
      case "select":
      case "selectstart":
        return 2;
      case "drag":
      case "dragenter":
      case "dragexit":
      case "dragleave":
      case "dragover":
      case "mousemove":
      case "mouseout":
      case "mouseover":
      case "pointermove":
      case "pointerout":
      case "pointerover":
      case "scroll":
      case "touchmove":
      case "wheel":
      case "mouseenter":
      case "mouseleave":
      case "pointerenter":
      case "pointerleave":
        return 8;
      case "message":
        switch (Cm()) {
          case Af:
            return 2;
          case Tf:
            return 8;
          case Cu:
          case Rm:
            return 32;
          case xf:
            return 268435456;
          default:
            return 32;
        }
      default:
        return 32;
    }
  }
  var df = !1, ja = null, _a = null, Na = null, Nu = /* @__PURE__ */ new Map(), Mu = /* @__PURE__ */ new Map(), Ma = [], Wv = "mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset".split(
    " "
  );
  function gm(l, t) {
    switch (l) {
      case "focusin":
      case "focusout":
        ja = null;
        break;
      case "dragenter":
      case "dragleave":
        _a = null;
        break;
      case "mouseover":
      case "mouseout":
        Na = null;
        break;
      case "pointerover":
      case "pointerout":
        Nu.delete(t.pointerId);
        break;
      case "gotpointercapture":
      case "lostpointercapture":
        Mu.delete(t.pointerId);
    }
  }
  function Ou(l, t, a, e, u, n) {
    return l === null || l.nativeEvent !== n ? (l = {
      blockedOn: t,
      domEventName: a,
      eventSystemFlags: e,
      nativeEvent: n,
      targetContainers: [u]
    }, t !== null && (t = te(t), t !== null && vm(t)), l) : (l.eventSystemFlags |= e, t = l.targetContainers, u !== null && t.indexOf(u) === -1 && t.push(u), l);
  }
  function kv(l, t, a, e, u) {
    switch (t) {
      case "focusin":
        return ja = Ou(
          ja,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "dragenter":
        return _a = Ou(
          _a,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "mouseover":
        return Na = Ou(
          Na,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "pointerover":
        var n = u.pointerId;
        return Nu.set(
          n,
          Ou(
            Nu.get(n) || null,
            l,
            t,
            a,
            e,
            u
          )
        ), !0;
      case "gotpointercapture":
        return n = u.pointerId, Mu.set(
          n,
          Ou(
            Mu.get(n) || null,
            l,
            t,
            a,
            e,
            u
          )
        ), !0;
    }
    return !1;
  }
  function Sm(l) {
    var t = le(l.target);
    if (t !== null) {
      var a = G(t);
      if (a !== null) {
        if (t = a.tag, t === 13) {
          if (t = Al(a), t !== null) {
            l.blockedOn = t, Df(l.priority, function() {
              ym(a);
            });
            return;
          }
        } else if (t === 31) {
          if (t = nl(a), t !== null) {
            l.blockedOn = t, Df(l.priority, function() {
              ym(a);
            });
            return;
          }
        } else if (t === 3 && a.stateNode.current.memoizedState.isDehydrated) {
          l.blockedOn = a.tag === 3 ? a.stateNode.containerInfo : null;
          return;
        }
      }
    }
    l.blockedOn = null;
  }
  function wn(l) {
    if (l.blockedOn !== null) return !1;
    for (var t = l.targetContainers; 0 < t.length; ) {
      var a = ff(l.nativeEvent);
      if (a === null) {
        a = l.nativeEvent;
        var e = new a.constructor(
          a.type,
          a
        );
        fi = e, a.target.dispatchEvent(e), fi = null;
      } else
        return t = te(a), t !== null && vm(t), l.blockedOn = a, !1;
      t.shift();
    }
    return !0;
  }
  function bm(l, t, a) {
    wn(l) && a.delete(t);
  }
  function Fv() {
    df = !1, ja !== null && wn(ja) && (ja = null), _a !== null && wn(_a) && (_a = null), Na !== null && wn(Na) && (Na = null), Nu.forEach(bm), Mu.forEach(bm);
  }
  function $n(l, t) {
    l.blockedOn === t && (l.blockedOn = null, df || (df = !0, M.unstable_scheduleCallback(
      M.unstable_NormalPriority,
      Fv
    )));
  }
  var Wn = null;
  function pm(l) {
    Wn !== l && (Wn = l, M.unstable_scheduleCallback(
      M.unstable_NormalPriority,
      function() {
        Wn === l && (Wn = null);
        for (var t = 0; t < l.length; t += 3) {
          var a = l[t], e = l[t + 1], u = l[t + 2];
          if (typeof e != "function") {
            if (sf(e || a) === null)
              continue;
            break;
          }
          var n = te(a);
          n !== null && (l.splice(t, 3), t -= 3, cc(
            n,
            {
              pending: !0,
              data: u,
              method: a.method,
              action: e
            },
            e,
            u
          ));
        }
      }
    ));
  }
  function qe(l) {
    function t(s) {
      return $n(s, l);
    }
    ja !== null && $n(ja, l), _a !== null && $n(_a, l), Na !== null && $n(Na, l), Nu.forEach(t), Mu.forEach(t);
    for (var a = 0; a < Ma.length; a++) {
      var e = Ma[a];
      e.blockedOn === l && (e.blockedOn = null);
    }
    for (; 0 < Ma.length && (a = Ma[0], a.blockedOn === null); )
      Sm(a), a.blockedOn === null && Ma.shift();
    if (a = (l.ownerDocument || l).$$reactFormReplay, a != null)
      for (e = 0; e < a.length; e += 3) {
        var u = a[e], n = a[e + 1], i = u[lt] || null;
        if (typeof n == "function")
          i || pm(a);
        else if (i) {
          var c = null;
          if (n && n.hasAttribute("formAction")) {
            if (u = n, i = n[lt] || null)
              c = i.formAction;
            else if (sf(u) !== null) continue;
          } else c = i.action;
          typeof c == "function" ? a[e + 1] = c : (a.splice(e, 3), e -= 3), pm(a);
        }
      }
  }
  function zm() {
    function l(n) {
      n.canIntercept && n.info === "react-transition" && n.intercept({
        handler: function() {
          return new Promise(function(i) {
            return u = i;
          });
        },
        focusReset: "manual",
        scroll: "manual"
      });
    }
    function t() {
      u !== null && (u(), u = null), e || setTimeout(a, 20);
    }
    function a() {
      if (!e && !navigation.transition) {
        var n = navigation.currentEntry;
        n && n.url != null && navigation.navigate(n.url, {
          state: n.getState(),
          info: "react-transition",
          history: "replace"
        });
      }
    }
    if (typeof navigation == "object") {
      var e = !1, u = null;
      return navigation.addEventListener("navigate", l), navigation.addEventListener("navigatesuccess", t), navigation.addEventListener("navigateerror", t), setTimeout(a, 100), function() {
        e = !0, navigation.removeEventListener("navigate", l), navigation.removeEventListener("navigatesuccess", t), navigation.removeEventListener("navigateerror", t), u !== null && (u(), u = null);
      };
    }
  }
  function of(l) {
    this._internalRoot = l;
  }
  kn.prototype.render = of.prototype.render = function(l) {
    var t = this._internalRoot;
    if (t === null) throw Error(g(409));
    var a = t.current, e = yt();
    mm(a, e, l, t, null, null);
  }, kn.prototype.unmount = of.prototype.unmount = function() {
    var l = this._internalRoot;
    if (l !== null) {
      this._internalRoot = null;
      var t = l.containerInfo;
      mm(l.current, 2, null, l, null, null), On(), t[Pa] = null;
    }
  };
  function kn(l) {
    this._internalRoot = l;
  }
  kn.prototype.unstable_scheduleHydration = function(l) {
    if (l) {
      var t = Of();
      l = { blockedOn: null, target: l, priority: t };
      for (var a = 0; a < Ma.length && t !== 0 && t < Ma[a].priority; a++) ;
      Ma.splice(a, 0, l), a === 0 && Sm(l);
    }
  };
  var Em = El.version;
  if (Em !== "19.2.8")
    throw Error(
      g(
        527,
        Em,
        "19.2.8"
      )
    );
  _.findDOMNode = function(l) {
    var t = l._reactInternals;
    if (t === void 0)
      throw typeof l.render == "function" ? Error(g(188)) : (l = Object.keys(l).join(","), Error(g(268, l)));
    return l = p(t), l = l !== null ? X(l) : null, l = l === null ? null : l.stateNode, l;
  };
  var Iv = {
    bundleType: 0,
    version: "19.2.8",
    rendererPackageName: "react-dom",
    currentDispatcherRef: b,
    reconcilerVersion: "19.2.8"
  };
  if (typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u") {
    var Fn = __REACT_DEVTOOLS_GLOBAL_HOOK__;
    if (!Fn.isDisabled && Fn.supportsFiber)
      try {
        Ge = Fn.inject(
          Iv
        ), ct = Fn;
      } catch {
      }
  }
  return Uu.createRoot = function(l, t) {
    if (!cl(l)) throw Error(g(299));
    var a = !1, e = "", u = Nd, n = Md, i = Od;
    return t != null && (t.unstable_strictMode === !0 && (a = !0), t.identifierPrefix !== void 0 && (e = t.identifierPrefix), t.onUncaughtError !== void 0 && (u = t.onUncaughtError), t.onCaughtError !== void 0 && (n = t.onCaughtError), t.onRecoverableError !== void 0 && (i = t.onRecoverableError)), t = dm(
      l,
      1,
      !1,
      null,
      null,
      a,
      e,
      null,
      u,
      n,
      i,
      zm
    ), l[Pa] = t.current, Kc(l), new of(t);
  }, Uu.hydrateRoot = function(l, t, a) {
    if (!cl(l)) throw Error(g(299));
    var e = !1, u = "", n = Nd, i = Md, c = Od, s = null;
    return a != null && (a.unstable_strictMode === !0 && (e = !0), a.identifierPrefix !== void 0 && (u = a.identifierPrefix), a.onUncaughtError !== void 0 && (n = a.onUncaughtError), a.onCaughtError !== void 0 && (i = a.onCaughtError), a.onRecoverableError !== void 0 && (c = a.onRecoverableError), a.formState !== void 0 && (s = a.formState)), t = dm(
      l,
      1,
      !0,
      t,
      a ?? null,
      e,
      u,
      s,
      n,
      i,
      c,
      zm
    ), t.context = om(null), a = t.current, e = yt(), e = li(e), u = va(e), u.callback = null, ya(a, u, e), a = e, t.current.lanes = a, Ze(t, a), Xt(t), l[Pa] = t.current, Kc(l), new kn(t);
  }, Uu.version = "19.2.8", Uu;
}
var Um;
function f0() {
  if (Um) return vf.exports;
  Um = 1;
  function M() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function"))
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(M);
      } catch (El) {
        console.error(El);
      }
  }
  return M(), vf.exports = c0(), vf.exports;
}
var s0 = f0();
const Hm = ["#ddebff", "#fff0c9", "#e8dfff", "#ddf4e5", "#ffe1e1", "#eee7ff"], Da = { reader: "sensors", door: "door_open", camera: "videocam" }, In = "#f59e0b", Sf = (M) => `${M}-${Date.now()}-${Math.random().toString(16).slice(2)}`, bf = (M) => M ? new Date(M.replace(" ", "T")).toLocaleTimeString("en-MY", { hour: "2-digit", minute: "2-digit", second: "2-digit" }) : "—", pf = (M) => {
  const El = String(M || "").replace("#", "");
  if (!/^[0-9a-f]{6}$/i.test(El)) return "#22d3ee";
  const [P, g, cl] = [0, 2, 4].map((p) => parseInt(El.slice(p, p + 2), 16) / 255), G = Math.max(P, g, cl), Al = Math.min(P, g, cl), nl = G - Al;
  let U = 0;
  return nl && (G === P ? U = 60 * ((g - cl) / nl % 6) : G === g ? U = 60 * ((cl - P) / nl + 2) : U = 60 * ((P - g) / nl + 4)), U < 0 && (U += 360), `hsl(${Math.round(U)} 100% 58%)`;
};
function d0({ root: M }) {
  const [El, P] = Hl.useState(!0), [g, cl] = Hl.useState(""), [G, Al] = Hl.useState("live"), [nl, U] = Hl.useState(null), [p, X] = Hl.useState({ width: 1e3, height: 590, zones: [], walls: [], assets: [], labels: [] }), [C, yl] = Hl.useState({ locations: [], subLocations: [], lanes: [], devices: [] }), [jl, Zl] = Hl.useState([]), [Cl, rt] = Hl.useState([]), [Yl, Ct] = Hl.useState({ design: M.dataset.canDesign === "1", publish: M.dataset.canPublish === "1" }), [Bl, Ql] = Hl.useState("select"), [Sl, bl] = Hl.useState(null), [w, Ll] = Hl.useState(null), [kl, Zt] = Hl.useState(1), [gt, zl] = Hl.useState(""), [Rt, St] = Hl.useState(!1), Pl = Hl.useRef(null), b = Hl.useRef(null);
  async function _() {
    try {
      const d = await fetch(M.dataset.bootstrapUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!d.ok) throw new Error(`Unable to load E-Map (${d.status})`);
      const N = (await d.json()).data;
      U(N.map), X(N.map.layout), yl(N.references), Zl(N.visitors || []), rt(N.movementLog || []), Ct(N.permissions || Yl), cl("");
    } catch (d) {
      cl(d.message || "Unable to load E-Map.");
    } finally {
      P(!1);
    }
  }
  async function Y(d = !1) {
    try {
      const x = await fetch(M.dataset.liveUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!x.ok) throw new Error("Live movement refresh failed.");
      const N = await x.json();
      Zl(N.data.visitors || []), rt(N.data.movementLog || []), d || zl(`Live data refreshed at ${(/* @__PURE__ */ new Date()).toLocaleTimeString("en-MY")}`);
    } catch (x) {
      d || zl(x.message);
    }
  }
  Hl.useEffect(() => {
    _();
  }, []), Hl.useEffect(() => {
    if (G !== "live") return;
    const d = window.setInterval(() => Y(!0), 1e4);
    return () => window.clearInterval(d);
  }, [G]);
  const K = p.zones.find((d) => d.id === Sl), ll = p.walls.find((d) => d.id === Sl), o = p.assets.find((d) => d.id === Sl), E = jl.find((d) => String(d.id) === String(w)), j = Hl.useMemo(() => {
    const d = {};
    for (const x of jl) {
      const N = p.zones.find(
        (R) => x.subLocationId && Number(R.subLocationId) === Number(x.subLocationId) || !x.subLocationId && x.locationId && Number(R.locationId) === Number(x.locationId)
      );
      N && (d[N.id] ||= [], d[N.id].push(x));
    }
    return d;
  }, [jl, p.zones]);
  function O(d, x) {
    const N = Pl.current?.getBoundingClientRect();
    return N ? {
      x: (d - N.left) / N.width * p.width,
      y: (x - N.top) / N.height * p.height
    } : { x: 0, y: 0 };
  }
  function Z(d) {
    if (!b.current || G !== "designer") return;
    const x = O(d.clientX, d.clientY), N = b.current;
    N.kind === "zone-move" ? X((R) => ({ ...R, zones: R.zones.map((tl) => tl.id === N.id ? {
      ...tl,
      x: Math.max(0, Math.min(R.width - tl.w, x.x - N.dx)),
      y: Math.max(0, Math.min(R.height - tl.h, x.y - N.dy))
    } : tl) })) : N.kind === "zone-resize" ? X((R) => ({ ...R, zones: R.zones.map((tl) => tl.id === N.id ? {
      ...tl,
      w: Math.max(80, x.x - tl.x),
      h: Math.max(60, x.y - tl.y)
    } : tl) })) : N.kind === "asset-move" ? X((R) => ({ ...R, assets: R.assets.map((tl) => tl.id === N.id ? { ...tl, x: x.x - N.dx, y: x.y - N.dy } : tl) })) : (N.kind === "wall-start" || N.kind === "wall-end") && X((R) => ({ ...R, walls: R.walls.map((tl) => tl.id === N.id ? N.kind === "wall-start" ? { ...tl, x1: x.x, y1: x.y } : { ...tl, x2: x.x, y2: x.y } : tl) }));
  }
  function J() {
    const d = Sf("zone"), x = { id: d, name: "NEW ZONE", x: 350, y: 220, w: 210, h: 130, color: Hm[p.zones.length % Hm.length], locationId: null, subLocationId: null };
    X((N) => ({ ...N, zones: [...N.zones, x] })), bl(d), Ql("select"), zl("Zone added. Drag it or use the blue corner to resize.");
  }
  function al() {
    const d = Sf("wall");
    X((x) => ({ ...x, walls: [...x.walls, { id: d, x1: 370, y1: 290, x2: 570, y2: 290 }] })), bl(d), Ql("select"), zl("Wall added. Drag either endpoint.");
  }
  function Rl(d) {
    const x = Sf("asset"), N = { reader: "RFID Reader", door: "Access Door", camera: "Camera / FR" };
    X((R) => ({ ...R, assets: [...R.assets, {
      id: x,
      type: d,
      label: N[d],
      x: 500,
      y: 280,
      laneId: null,
      deviceAssignmentId: null,
      fromZoneId: null,
      toZoneId: null,
      transitionMode: "bidirectional"
    }] })), bl(x), Ql("select"), zl(`${N[d]} added.`);
  }
  function fl(d) {
    X((x) => ({ ...x, zones: x.zones.map((N) => N.id === Sl ? { ...N, ...d } : N) }));
  }
  function qt(d) {
    X((x) => ({ ...x, walls: x.walls.map((N) => N.id === Sl ? { ...N, ...d } : N) }));
  }
  function bt(d) {
    X((x) => ({ ...x, assets: x.assets.map((N) => N.id === Sl ? { ...N, ...d } : N) }));
  }
  function Ua(d) {
    if (!d) return C.devices;
    const x = C.lanes.find((R) => Number(R.id) === Number(d));
    if (!x) return [];
    if (x.rfid_reader_ip)
      return C.devices.filter((R) => R.ip_address === x.rfid_reader_ip);
    const N = new Set(
      C.subLocations.filter((R) => Number(R.location_id) === Number(x.location_id)).map((R) => Number(R.id))
    );
    return C.devices.filter((R) => N.has(Number(R.location_id)));
  }
  function Hu(d) {
    const x = d ? Number(d) : null, R = Ua(x).some((tl) => Number(tl.id) === Number(o?.deviceAssignmentId));
    bt({
      laneId: x,
      deviceAssignmentId: R ? o.deviceAssignmentId : null
    });
  }
  function Ot() {
    X((d) => ({
      ...d,
      zones: d.zones.filter((x) => x.id !== Sl),
      walls: d.walls.filter((x) => x.id !== Sl),
      assets: d.assets.filter((x) => x.id !== Sl)
    })), bl(null);
  }
  async function Ia(d = !1) {
    if (!(!nl || !Yl.design)) {
      St(!0), zl("");
      try {
        const x = M.dataset.mapUrlTemplate.replace("__MAP_ID__", String(nl.id)), N = await fetch(x, {
          method: "PUT",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json", Accept: "application/json" },
          body: JSON.stringify({ layout: p, publish: d, name: nl.name, premiseName: nl.premiseName, floorName: nl.floorName })
        }), R = await N.json();
        if (!N.ok) throw new Error(R.message || "Unable to save map.");
        U(R.data.map), X(R.data.map.layout), zl(R.message);
      } catch (x) {
        zl(x.message);
      } finally {
        St(!1);
      }
    }
  }
  return El ? /* @__PURE__ */ f.jsxs("div", { className: "emap-state", children: [
    /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "map" }),
    /* @__PURE__ */ f.jsx("b", { children: "Loading E-Map…" })
  ] }) : g ? /* @__PURE__ */ f.jsxs("div", { className: "emap-state error", children: [
    /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "error" }),
    /* @__PURE__ */ f.jsx("b", { children: g }),
    /* @__PURE__ */ f.jsx("button", { onClick: _, children: "Try again" })
  ] }) : /* @__PURE__ */ f.jsxs("div", { className: "emap-app", children: [
    /* @__PURE__ */ f.jsxs("header", { className: "emap-header", children: [
      /* @__PURE__ */ f.jsxs("div", { children: [
        /* @__PURE__ */ f.jsxs("p", { children: [
          "E-MAP / ",
          nl.premiseName.toUpperCase()
        ] }),
        /* @__PURE__ */ f.jsx("h1", { children: nl.floorName })
      ] }),
      /* @__PURE__ */ f.jsxs("div", { className: "emap-header-actions", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "emap-tabs", children: [
          /* @__PURE__ */ f.jsxs("button", { className: G === "live" ? "active" : "", onClick: () => Al("live"), children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "location_on" }),
            "Live Movement"
          ] }),
          Yl.design && /* @__PURE__ */ f.jsxs("button", { className: G === "designer" ? "active" : "", onClick: () => Al("designer"), children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "draw" }),
            "Map Designer"
          ] })
        ] }),
        G === "live" ? /* @__PURE__ */ f.jsxs("button", { className: "emap-primary green", onClick: () => Y(!1), children: [
          /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "refresh" }),
          "Refresh live"
        ] }) : /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
          /* @__PURE__ */ f.jsx("button", { className: "emap-secondary", disabled: Rt, onClick: () => Ia(!1), children: "Save draft" }),
          Yl.publish && /* @__PURE__ */ f.jsx("button", { className: "emap-primary", disabled: Rt, onClick: () => Ia(!0), children: "Publish" })
        ] })
      ] })
    ] }),
    /* @__PURE__ */ f.jsxs("div", { className: "emap-status", children: [
      /* @__PURE__ */ f.jsx("span", { className: "status-dot" }),
      gt || (G === "live" ? "Live movement refreshes every 10 seconds" : "Edit the floor layout, then save or publish"),
      /* @__PURE__ */ f.jsxs("span", { children: [
        nl.status,
        " · v",
        nl.version
      ] })
    ] }),
    /* @__PURE__ */ f.jsxs("div", { className: `emap-grid ${G}`, children: [
      G === "designer" && /* @__PURE__ */ f.jsxs("aside", { className: "emap-panel emap-tools", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
          /* @__PURE__ */ f.jsx("h2", { children: "Map tools" }),
          /* @__PURE__ */ f.jsx("span", { children: "DESIGN" })
        ] }),
        /* @__PURE__ */ f.jsxs("button", { className: Bl === "select" ? "active" : "", onClick: () => Ql("select"), children: [
          /* @__PURE__ */ f.jsx("i", { children: "↖" }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("b", { children: "Select" }),
            /* @__PURE__ */ f.jsx("small", { children: "Move and inspect objects" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("button", { onClick: J, children: [
          /* @__PURE__ */ f.jsx("i", { children: "⬚" }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("b", { children: "Zone" }),
            /* @__PURE__ */ f.jsx("small", { children: "Create a coloured area" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("button", { onClick: al, children: [
          /* @__PURE__ */ f.jsx("i", { children: "╱" }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("b", { children: "Wall" }),
            /* @__PURE__ */ f.jsx("small", { children: "Add a structural line" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "asset-buttons", children: [
          /* @__PURE__ */ f.jsx("b", { children: "Add asset" }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Rl("door"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "door", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.door }) }),
            "Access door"
          ] }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Rl("reader"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "reader", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.reader }) }),
            "RFID reader"
          ] }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Rl("camera"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "camera", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.camera }) }),
            "Camera / FR"
          ] })
        ] })
      ] }),
      /* @__PURE__ */ f.jsxs("section", { className: "emap-panel map-card", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "map-topbar", children: [
          /* @__PURE__ */ f.jsxs("div", { children: [
            /* @__PURE__ */ f.jsx("b", { children: nl.name }),
            /* @__PURE__ */ f.jsx("small", { children: G === "live" ? `${jl.length} visitor${jl.length === 1 ? "" : "s"} currently mapped` : "Drag, resize and link map objects" })
          ] }),
          /* @__PURE__ */ f.jsxs("div", { className: "zoom", children: [
            /* @__PURE__ */ f.jsx("button", { onClick: () => Zt(Math.max(0.7, kl - 0.1)), children: "−" }),
            /* @__PURE__ */ f.jsxs("span", { children: [
              Math.round(kl * 100),
              "%"
            ] }),
            /* @__PURE__ */ f.jsx("button", { onClick: () => Zt(Math.min(1.4, kl + 0.1)), children: "+" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "emap-canvas", children: [
          /* @__PURE__ */ f.jsxs("svg", { ref: Pl, viewBox: `0 0 ${p.width} ${p.height}`, style: { transform: `scale(${kl})` }, onPointerMove: Z, onPointerUp: () => b.current = null, onPointerLeave: () => b.current = null, children: [
            p.zones.map((d) => {
              const x = d.hazardous ? In : pf(d.color);
              return /* @__PURE__ */ f.jsxs("g", { className: G === "designer" ? "movable" : "", onClick: () => G === "designer" && bl(d.id), onPointerDown: (N) => {
                if (G !== "designer") return;
                const R = O(N.clientX, N.clientY);
                b.current = { id: d.id, kind: "zone-move", dx: R.x - d.x, dy: R.y - d.y }, bl(d.id);
              }, children: [
                /* @__PURE__ */ f.jsx(
                  "rect",
                  {
                    className: "zone-shape",
                    x: d.x,
                    y: d.y,
                    width: d.w,
                    height: d.h,
                    rx: "4",
                    fill: d.hazardous ? In : "transparent",
                    fillOpacity: d.hazardous ? 0.04 : 0,
                    stroke: x,
                    strokeDasharray: d.hazardous ? "9 6" : void 0,
                    strokeWidth: Sl === d.id && G === "designer" ? 4 : 2.5,
                    style: { filter: `drop-shadow(0 0 2px ${x}) drop-shadow(0 0 ${Sl === d.id && G === "designer" ? 7 : 4}px ${x})` }
                  }
                ),
                /* @__PURE__ */ f.jsx("text", { x: d.x + 12, y: d.y + 24, textAnchor: "start", className: "zone-name", children: d.name }),
                d.hazardous && /* @__PURE__ */ f.jsxs("g", { className: "hazard-label", transform: `translate(${d.x + d.w - 18} ${d.y + 19})`, children: [
                  /* @__PURE__ */ f.jsx("text", { textAnchor: "middle", className: "hazard-symbol", children: "warning" }),
                  /* @__PURE__ */ f.jsx("text", { x: "-21", y: "3", textAnchor: "end", className: "hazard-text", children: "HAZARDOUS" })
                ] }),
                G === "designer" && Sl === d.id && /* @__PURE__ */ f.jsxs("g", { transform: `translate(${d.x + d.w} ${d.y + d.h})`, className: "handle", onPointerDown: (N) => {
                  N.stopPropagation(), b.current = { id: d.id, kind: "zone-resize" };
                }, children: [
                  /* @__PURE__ */ f.jsx("circle", { r: "10" }),
                  /* @__PURE__ */ f.jsx("path", { d: "M-4 4L4-4M0 4L4 0" })
                ] })
              ] }, d.id);
            }),
            p.walls.map((d) => /* @__PURE__ */ f.jsxs("g", { onClick: () => G === "designer" && bl(d.id), children: [
              /* @__PURE__ */ f.jsx("line", { x1: d.x1, y1: d.y1, x2: d.x2, y2: d.y2, stroke: "#515b68", strokeWidth: "9" }),
              /* @__PURE__ */ f.jsx("line", { x1: d.x1, y1: d.y1, x2: d.x2, y2: d.y2, stroke: "#f8fafc", strokeWidth: "3" }),
              G === "designer" && Sl === d.id && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
                /* @__PURE__ */ f.jsx("circle", { className: "wall-handle", cx: d.x1, cy: d.y1, r: "10", onPointerDown: (x) => {
                  x.stopPropagation(), b.current = { id: d.id, kind: "wall-start" };
                } }),
                /* @__PURE__ */ f.jsx("circle", { className: "wall-handle", cx: d.x2, cy: d.y2, r: "10", onPointerDown: (x) => {
                  x.stopPropagation(), b.current = { id: d.id, kind: "wall-end" };
                } })
              ] })
            ] }, d.id)),
            p.assets.map((d) => /* @__PURE__ */ f.jsxs("g", { className: "map-asset", transform: `translate(${d.x} ${d.y})`, onClick: () => G === "designer" && bl(d.id), onPointerDown: (x) => {
              if (G !== "designer") return;
              const N = O(x.clientX, x.clientY);
              b.current = { id: d.id, kind: "asset-move", dx: N.x - d.x, dy: N.y - d.y }, bl(d.id);
            }, children: [
              /* @__PURE__ */ f.jsx("rect", { x: "-17", y: "-17", width: "34", height: "34", rx: "8", className: d.type }),
              /* @__PURE__ */ f.jsx("text", { y: "7", textAnchor: "middle", className: "asset-symbol", children: Da[d.type] || "device_unknown" })
            ] }, d.id)),
            G === "live" && Object.entries(j).flatMap(([d, x]) => {
              const N = p.zones.find((R) => R.id === d);
              return x.map((R, tl) => {
                const Ha = N.x + 45 + tl % 3 * 62, Ye = N.y + 75 + Math.floor(tl / 3) * 70;
                return /* @__PURE__ */ f.jsxs("g", { className: "visitor-marker", transform: `translate(${Ha} ${Ye})`, onPointerEnter: () => Ll(String(R.id)), onPointerLeave: () => Ll(null), children: [
                  /* @__PURE__ */ f.jsx("circle", { r: "20" }),
                  /* @__PURE__ */ f.jsx("text", { y: "5", textAnchor: "middle", children: R.initials }),
                  /* @__PURE__ */ f.jsx("rect", { x: "-40", y: "25", width: "80", height: "20", rx: "10" }),
                  /* @__PURE__ */ f.jsx("text", { y: "39", textAnchor: "middle", className: "visitor-label", children: R.name })
                ] }, R.id);
              });
            }),
            G === "live" && E && (() => {
              const d = p.zones.find((Be) => (j[Be.id] || []).some((Fl) => String(Fl.id) === String(E.id)));
              if (!d) return null;
              const N = (j[d.id] || []).findIndex((Be) => String(Be.id) === String(E.id)), R = d.x + 45 + N % 3 * 62, tl = d.y + 75 + Math.floor(N / 3) * 70, Ha = R > p.width - 300 ? R - 265 : R + 32, Ye = Math.max(16, Math.min(p.height - 170, tl - 60));
              return /* @__PURE__ */ f.jsxs("g", { className: "visitor-popover", transform: `translate(${Ha} ${Ye})`, pointerEvents: "none", children: [
                /* @__PURE__ */ f.jsx("rect", { width: "235", height: "150", rx: "10" }),
                /* @__PURE__ */ f.jsx("circle", { cx: "23", cy: "23", r: "12" }),
                /* @__PURE__ */ f.jsx("text", { x: "23", y: "27", textAnchor: "middle", className: "initials", children: E.initials }),
                /* @__PURE__ */ f.jsx("text", { x: "43", y: "20", className: "name", children: E.name }),
                /* @__PURE__ */ f.jsx("text", { x: "43", y: "34", className: "company", children: E.company }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "63", className: "label", children: "TIME IN" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "63", className: "value", children: bf(E.timeIn) }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "85", className: "label", children: "HOST" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "85", className: "value", children: E.host }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "107", className: "label", children: "CURRENT ZONE" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "107", className: "value", children: d.name }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "129", className: "label", children: "LAST DETECTED" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "129", className: "value", children: bf(E.lastSeen) }),
                /* @__PURE__ */ f.jsx("circle", { cx: "18", cy: "142", r: "3", className: "live-dot" }),
                /* @__PURE__ */ f.jsx("text", { x: "27", y: "145", className: "live-label", children: "Currently in premise" })
              ] });
            })()
          ] }),
          G === "live" && jl.length === 0 && /* @__PURE__ */ f.jsxs("div", { className: "canvas-empty", children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "sensors_off" }),
            /* @__PURE__ */ f.jsx("b", { children: "No active visitor positions yet" }),
            /* @__PURE__ */ f.jsx("small", { children: "Visitors appear after a mapped RFID or QR movement event." })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "map-legend", children: [
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "reader", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.reader }) }),
            "RFID reader"
          ] }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "door", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.door }) }),
            "Access door"
          ] }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "camera", children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da.camera }) }),
            "Camera / FR"
          ] }),
          /* @__PURE__ */ f.jsx("em", { children: "Visitor positions show the latest detected zone." })
        ] })
      ] }),
      G === "designer" ? /* @__PURE__ */ f.jsxs("aside", { className: "emap-panel inspector", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
          /* @__PURE__ */ f.jsx("h2", { children: "Inspector" }),
          /* @__PURE__ */ f.jsx("span", { children: "PROPERTIES" })
        ] }),
        !K && !ll && !o && /* @__PURE__ */ f.jsxs("div", { className: "empty-inspector", children: [
          /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "touch_app" }),
          "Select an object on the map"
        ] }),
        K && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Zone name",
            /* @__PURE__ */ f.jsx("input", { value: K.name, onChange: (d) => fl({ name: d.target.value.toUpperCase() }) })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Zone colour",
            /* @__PURE__ */ f.jsx("input", { type: "color", value: K.color, onChange: (d) => fl({ color: d.target.value }) })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { className: "hazard-toggle", children: [
            /* @__PURE__ */ f.jsxs("span", { children: [
              /* @__PURE__ */ f.jsx("input", { type: "checkbox", checked: !!K.hazardous, onChange: (d) => fl({ hazardous: d.target.checked }) }),
              "Hazardous zone"
            ] }),
            /* @__PURE__ */ f.jsx("small", { children: "Shows an amber warning area in Live mode" })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Location",
            /* @__PURE__ */ f.jsxs("select", { value: K.locationId || "", onChange: (d) => fl({ locationId: d.target.value ? Number(d.target.value) : null, subLocationId: null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              C.locations.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.location_access }, d.id))
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Sub-location / zone",
            /* @__PURE__ */ f.jsxs("select", { value: K.subLocationId || "", onChange: (d) => fl({ subLocationId: d.target.value ? Number(d.target.value) : null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              C.subLocations.filter((d) => !K.locationId || Number(d.location_id) === Number(K.locationId)).map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.name }, d.id))
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("div", { className: "field-grid", children: [
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Width",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(K.w), onChange: (d) => fl({ w: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Height",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(K.h), onChange: (d) => fl({ h: Number(d.target.value) }) })
            ] })
          ] }),
          /* @__PURE__ */ f.jsx("button", { className: "delete-button", onClick: Ot, children: "Delete zone" })
        ] }),
        ll && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
          /* @__PURE__ */ f.jsxs("div", { className: "object-summary", children: [
            /* @__PURE__ */ f.jsx("i", { children: "╱" }),
            /* @__PURE__ */ f.jsxs("span", { children: [
              /* @__PURE__ */ f.jsx("b", { children: "Structural wall" }),
              /* @__PURE__ */ f.jsx("small", { children: "Drag either blue endpoint" })
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("div", { className: "field-grid", children: [
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Start X",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(ll.x1), onChange: (d) => qt({ x1: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Start Y",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(ll.y1), onChange: (d) => qt({ y1: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "End X",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(ll.x2), onChange: (d) => qt({ x2: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "End Y",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(ll.y2), onChange: (d) => qt({ y2: Number(d.target.value) }) })
            ] })
          ] }),
          /* @__PURE__ */ f.jsx("button", { className: "delete-button", onClick: Ot, children: "Delete wall" })
        ] }),
        o && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Asset label",
            /* @__PURE__ */ f.jsx("input", { value: o.label, onChange: (d) => bt({ label: d.target.value }) })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Asset type",
            /* @__PURE__ */ f.jsxs("select", { value: o.type, onChange: (d) => bt({ type: d.target.value }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "reader", children: "RFID reader" }),
              /* @__PURE__ */ f.jsx("option", { value: "door", children: "Access door" }),
              /* @__PURE__ */ f.jsx("option", { value: "camera", children: "Camera / FR" })
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Lane / door",
            /* @__PURE__ */ f.jsxs("select", { value: o.laneId || "", onChange: (d) => Hu(d.target.value), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              C.lanes.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.lane }, d.id))
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Device assignment",
            /* @__PURE__ */ f.jsxs("select", { value: o.deviceAssignmentId || "", onChange: (d) => bt({ deviceAssignmentId: d.target.value ? Number(d.target.value) : null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              Ua(o.laneId).length === 0 && o.laneId && /* @__PURE__ */ f.jsx("option", { disabled: !0, children: "No devices found in this lane area" }),
              Ua(o.laneId).map((d) => /* @__PURE__ */ f.jsxs("option", { value: d.id, children: [
                d.device_id || d.ip_address,
                " · ",
                d.type
              ] }, d.id))
            ] })
          ] }),
          o.type !== "camera" && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
            /* @__PURE__ */ f.jsxs("label", { children: [
              "From zone",
              /* @__PURE__ */ f.jsxs("select", { value: o.fromZoneId || "", onChange: (d) => bt({ fromZoneId: d.target.value || null }), children: [
                /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
                p.zones.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.name }, d.id))
              ] })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "To zone",
              /* @__PURE__ */ f.jsxs("select", { value: o.toZoneId || "", onChange: (d) => bt({ toZoneId: d.target.value || null }), children: [
                /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
                p.zones.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.name }, d.id))
              ] })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Movement mode",
              /* @__PURE__ */ f.jsxs("select", { value: o.transitionMode || "bidirectional", onChange: (d) => bt({ transitionMode: d.target.value }), children: [
                /* @__PURE__ */ f.jsx("option", { value: "bidirectional", children: "Bidirectional (A ↔ B)" }),
                /* @__PURE__ */ f.jsx("option", { value: "one_way", children: "One way (A → B)" })
              ] })
            ] })
          ] }),
          /* @__PURE__ */ f.jsx("button", { className: "delete-button", onClick: Ot, children: "Delete asset" })
        ] })
      ] }) : /* @__PURE__ */ f.jsxs("aside", { className: "live-sidebar", children: [
        /* @__PURE__ */ f.jsxs("section", { className: "emap-panel", children: [
          /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
            /* @__PURE__ */ f.jsx("h2", { children: "Zone occupancy" }),
            /* @__PURE__ */ f.jsx("span", { children: "LIVE" })
          ] }),
          /* @__PURE__ */ f.jsx("div", { className: "occupancy-list", children: p.zones.map((d) => /* @__PURE__ */ f.jsxs("div", { children: [
            /* @__PURE__ */ f.jsx("i", { style: { background: d.hazardous ? In : pf(d.color) } }),
            /* @__PURE__ */ f.jsxs("span", { children: [
              d.name,
              d.hazardous ? " · HAZARD" : ""
            ] }),
            /* @__PURE__ */ f.jsx("b", { children: j[d.id]?.length || 0 })
          ] }, d.id)) })
        ] }),
        /* @__PURE__ */ f.jsxs("section", { className: "emap-panel", children: [
          /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
            /* @__PURE__ */ f.jsx("h2", { children: "Mapped assets" }),
            /* @__PURE__ */ f.jsx("span", { children: p.assets.length })
          ] }),
          /* @__PURE__ */ f.jsxs("div", { className: "mapped-assets", children: [
            p.assets.slice(0, 6).map((d) => /* @__PURE__ */ f.jsxs("div", { children: [
              /* @__PURE__ */ f.jsx("i", { className: d.type, children: /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: Da[d.type] || "device_unknown" }) }),
              /* @__PURE__ */ f.jsxs("span", { children: [
                /* @__PURE__ */ f.jsx("b", { children: d.label }),
                /* @__PURE__ */ f.jsx("small", { children: d.deviceAssignmentId ? "Linked device" : "Not linked" })
              ] })
            ] }, d.id)),
            p.assets.length === 0 && /* @__PURE__ */ f.jsx("p", { children: "No assets placed on this map." })
          ] })
        ] })
      ] })
    ] }),
    G === "live" && /* @__PURE__ */ f.jsxs("section", { className: "emap-panel movement-log zone-occupancy", children: [
      /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
        /* @__PURE__ */ f.jsx("h2", { children: "Zone occupancy" }),
        /* @__PURE__ */ f.jsxs("span", { children: [
          jl.length,
          " PEOPLE ON MAP"
        ] })
      ] }),
      /* @__PURE__ */ f.jsx("div", { className: "occupancy-cards", children: p.zones.map((d) => {
        const x = j[d.id]?.length || 0, N = C.subLocations.find((tl) => Number(tl.id) === Number(d.subLocationId)), R = d.hazardous ? In : pf(d.color);
        return /* @__PURE__ */ f.jsxs("article", { className: `occupancy-card${d.hazardous ? " hazardous" : ""}`, style: { borderTopColor: R }, children: [
          /* @__PURE__ */ f.jsxs("div", { children: [
            /* @__PURE__ */ f.jsx("span", { className: "occupancy-colour", style: { background: R } }),
            /* @__PURE__ */ f.jsx("b", { children: d.name })
          ] }),
          /* @__PURE__ */ f.jsx("strong", { children: x }),
          /* @__PURE__ */ f.jsx("small", { children: d.hazardous ? "HAZARDOUS ZONE" : x === 1 ? "PERSON" : "PEOPLE" }),
          /* @__PURE__ */ f.jsx("p", { children: N?.name || "Not linked" })
        ] }, d.id);
      }) })
    ] }),
    G === "live" && /* @__PURE__ */ f.jsxs("section", { className: "emap-panel movement-log", children: [
      /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
        /* @__PURE__ */ f.jsx("h2", { children: "Live movement log" }),
        /* @__PURE__ */ f.jsxs("span", { children: [
          "LATEST ",
          Cl.length
        ] })
      ] }),
      /* @__PURE__ */ f.jsxs("div", { className: "log-head", children: [
        /* @__PURE__ */ f.jsx("span", { children: "VISITOR" }),
        /* @__PURE__ */ f.jsx("span", { children: "ZONE / DOOR" }),
        /* @__PURE__ */ f.jsx("span", { children: "ACTION" }),
        /* @__PURE__ */ f.jsx("span", { children: "TIME" })
      ] }),
      Cl.slice(0, 6).map((d) => /* @__PURE__ */ f.jsxs("div", { className: "log-row", children: [
        /* @__PURE__ */ f.jsxs("span", { children: [
          /* @__PURE__ */ f.jsx("i", { children: d.name.split(/\s+/).slice(0, 2).map((x) => x[0]).join("") }),
          /* @__PURE__ */ f.jsx("b", { children: d.name })
        ] }),
        /* @__PURE__ */ f.jsx("span", { children: d.zone }),
        /* @__PURE__ */ f.jsx("span", { className: `action ${d.action}`, children: d.action.replaceAll("_", " ") }),
        /* @__PURE__ */ f.jsx("span", { children: bf(d.time) })
      ] }, d.id)),
      Cl.length === 0 && /* @__PURE__ */ f.jsx("div", { className: "empty-log", children: "No RFID or QR movement has been recorded yet." })
    ] })
  ] });
}
const zf = document.getElementById("emap-root");
zf && s0.createRoot(zf).render(/* @__PURE__ */ f.jsx(d0, { root: zf }));
