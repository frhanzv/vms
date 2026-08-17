var df = { exports: {} }, Ou = {};
var pm;
function Fv() {
  if (pm) return Ou;
  pm = 1;
  var N = /* @__PURE__ */ Symbol.for("react.transitional.element"), Rl = /* @__PURE__ */ Symbol.for("react.fragment");
  function cl(g, gl, G) {
    var _l = null;
    if (G !== void 0 && (_l = "" + G), gl.key !== void 0 && (_l = "" + gl.key), "key" in gl) {
      G = {};
      for (var hl in gl)
        hl !== "key" && (G[hl] = gl[hl]);
    } else G = gl;
    return gl = G.ref, {
      $$typeof: N,
      type: g,
      key: _l,
      ref: gl !== void 0 ? gl : null,
      props: G
    };
  }
  return Ou.Fragment = Rl, Ou.jsx = cl, Ou.jsxs = cl, Ou;
}
var zm;
function Iv() {
  return zm || (zm = 1, df.exports = Fv()), df.exports;
}
var f = Iv(), of = { exports: {} }, Z = {};
var Em;
function Pv() {
  if (Em) return Z;
  Em = 1;
  var N = /* @__PURE__ */ Symbol.for("react.transitional.element"), Rl = /* @__PURE__ */ Symbol.for("react.portal"), cl = /* @__PURE__ */ Symbol.for("react.fragment"), g = /* @__PURE__ */ Symbol.for("react.strict_mode"), gl = /* @__PURE__ */ Symbol.for("react.profiler"), G = /* @__PURE__ */ Symbol.for("react.consumer"), _l = /* @__PURE__ */ Symbol.for("react.context"), hl = /* @__PURE__ */ Symbol.for("react.forward_ref"), C = /* @__PURE__ */ Symbol.for("react.suspense"), p = /* @__PURE__ */ Symbol.for("react.memo"), X = /* @__PURE__ */ Symbol.for("react.lazy"), R = /* @__PURE__ */ Symbol.for("react.activity"), vl = Symbol.iterator;
  function Al(o) {
    return o === null || typeof o != "object" ? null : (o = vl && o[vl] || o["@@iterator"], typeof o == "function" ? o : null);
  }
  var Ql = {
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
    this.props = o, this.context = E, this.refs = rt, this.updater = j || Ql;
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
    this.props = o, this.context = E, this.refs = rt, this.updater = j || Ql;
  }
  var Zl = Bl.prototype = new Ct();
  Zl.constructor = Bl, Cl(Zl, Yl.prototype), Zl.isPureReactComponent = !0;
  var Sl = Array.isArray;
  function bl() {
  }
  var w = { H: null, A: null, T: null, S: null }, Ll = Object.prototype.hasOwnProperty;
  function kl(o, E, j) {
    var O = j.ref;
    return {
      $$typeof: N,
      type: o,
      key: E,
      ref: O !== void 0 ? O : null,
      props: j
    };
  }
  function Qt(o, E) {
    return kl(o.type, E, o.props);
  }
  function gt(o) {
    return typeof o == "object" && o !== null && o.$$typeof === N;
  }
  function zl(o) {
    var E = { "=": "=0", ":": "=2" };
    return "$" + o.replace(/[=:]/g, function(j) {
      return E[j];
    });
  }
  var Ht = /\/+/g;
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
  function b(o, E, j, O, Q) {
    var J = typeof o;
    (J === "undefined" || J === "boolean") && (o = null);
    var ll = !1;
    if (o === null) ll = !0;
    else
      switch (J) {
        case "bigint":
        case "string":
        case "number":
          ll = !0;
          break;
        case "object":
          switch (o.$$typeof) {
            case N:
            case Rl:
              ll = !0;
              break;
            case X:
              return ll = o._init, b(
                ll(o._payload),
                E,
                j,
                O,
                Q
              );
          }
      }
    if (ll)
      return Q = Q(o), ll = O === "" ? "." + St(o, 0) : O, Sl(Q) ? (j = "", ll != null && (j = ll.replace(Ht, "$&/") + "/"), b(Q, E, j, "", function(qt) {
        return qt;
      })) : Q != null && (gt(Q) && (Q = Qt(
        Q,
        j + (Q.key == null || o && o.key === Q.key ? "" : ("" + Q.key).replace(
          Ht,
          "$&/"
        ) + "/") + ll
      )), E.push(Q)), 1;
    ll = 0;
    var Hl = O === "" ? "." : O + ":";
    if (Sl(o))
      for (var fl = 0; fl < o.length; fl++)
        O = o[fl], J = Hl + St(O, fl), ll += b(
          O,
          E,
          j,
          J,
          Q
        );
    else if (fl = Al(o), typeof fl == "function")
      for (o = fl.call(o), fl = 0; !(O = o.next()).done; )
        O = O.value, J = Hl + St(O, fl++), ll += b(
          O,
          E,
          j,
          J,
          Q
        );
    else if (J === "object") {
      if (typeof o.then == "function")
        return b(
          Pl(o),
          E,
          j,
          O,
          Q
        );
      throw E = String(o), Error(
        "Objects are not valid as a React child (found: " + (E === "[object Object]" ? "object with keys {" + Object.keys(o).join(", ") + "}" : E) + "). If you meant to render a collection of children, use an array instead."
      );
    }
    return ll;
  }
  function _(o, E, j) {
    if (o == null) return o;
    var O = [], Q = 0;
    return b(o, O, "", "", function(J) {
      return E.call(j, J, Q++);
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
  }, P = {
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
  return Z.Activity = R, Z.Children = P, Z.Component = Yl, Z.Fragment = cl, Z.Profiler = gl, Z.PureComponent = Bl, Z.StrictMode = g, Z.Suspense = C, Z.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = w, Z.__COMPILER_RUNTIME = {
    __proto__: null,
    c: function(o) {
      return w.H.useMemoCache(o);
    }
  }, Z.cache = function(o) {
    return function() {
      return o.apply(null, arguments);
    };
  }, Z.cacheSignal = function() {
    return null;
  }, Z.cloneElement = function(o, E, j) {
    if (o == null)
      throw Error(
        "The argument must be a React element, but you passed " + o + "."
      );
    var O = Cl({}, o.props), Q = o.key;
    if (E != null)
      for (J in E.key !== void 0 && (Q = "" + E.key), E)
        !Ll.call(E, J) || J === "key" || J === "__self" || J === "__source" || J === "ref" && E.ref === void 0 || (O[J] = E[J]);
    var J = arguments.length - 2;
    if (J === 1) O.children = j;
    else if (1 < J) {
      for (var ll = Array(J), Hl = 0; Hl < J; Hl++)
        ll[Hl] = arguments[Hl + 2];
      O.children = ll;
    }
    return kl(o.type, Q, O);
  }, Z.createContext = function(o) {
    return o = {
      $$typeof: _l,
      _currentValue: o,
      _currentValue2: o,
      _threadCount: 0,
      Provider: null,
      Consumer: null
    }, o.Provider = o, o.Consumer = {
      $$typeof: G,
      _context: o
    }, o;
  }, Z.createElement = function(o, E, j) {
    var O, Q = {}, J = null;
    if (E != null)
      for (O in E.key !== void 0 && (J = "" + E.key), E)
        Ll.call(E, O) && O !== "key" && O !== "__self" && O !== "__source" && (Q[O] = E[O]);
    var ll = arguments.length - 2;
    if (ll === 1) Q.children = j;
    else if (1 < ll) {
      for (var Hl = Array(ll), fl = 0; fl < ll; fl++)
        Hl[fl] = arguments[fl + 2];
      Q.children = Hl;
    }
    if (o && o.defaultProps)
      for (O in ll = o.defaultProps, ll)
        Q[O] === void 0 && (Q[O] = ll[O]);
    return kl(o, J, Q);
  }, Z.createRef = function() {
    return { current: null };
  }, Z.forwardRef = function(o) {
    return { $$typeof: hl, render: o };
  }, Z.isValidElement = gt, Z.lazy = function(o) {
    return {
      $$typeof: X,
      _payload: { _status: -1, _result: o },
      _init: Y
    };
  }, Z.memo = function(o, E) {
    return {
      $$typeof: p,
      type: o,
      compare: E === void 0 ? null : E
    };
  }, Z.startTransition = function(o) {
    var E = w.T, j = {};
    w.T = j;
    try {
      var O = o(), Q = w.S;
      Q !== null && Q(j, O), typeof O == "object" && O !== null && typeof O.then == "function" && O.then(bl, K);
    } catch (J) {
      K(J);
    } finally {
      E !== null && j.types !== null && (E.types = j.types), w.T = E;
    }
  }, Z.unstable_useCacheRefresh = function() {
    return w.H.useCacheRefresh();
  }, Z.use = function(o) {
    return w.H.use(o);
  }, Z.useActionState = function(o, E, j) {
    return w.H.useActionState(o, E, j);
  }, Z.useCallback = function(o, E) {
    return w.H.useCallback(o, E);
  }, Z.useContext = function(o) {
    return w.H.useContext(o);
  }, Z.useDebugValue = function() {
  }, Z.useDeferredValue = function(o, E) {
    return w.H.useDeferredValue(o, E);
  }, Z.useEffect = function(o, E) {
    return w.H.useEffect(o, E);
  }, Z.useEffectEvent = function(o) {
    return w.H.useEffectEvent(o);
  }, Z.useId = function() {
    return w.H.useId();
  }, Z.useImperativeHandle = function(o, E, j) {
    return w.H.useImperativeHandle(o, E, j);
  }, Z.useInsertionEffect = function(o, E) {
    return w.H.useInsertionEffect(o, E);
  }, Z.useLayoutEffect = function(o, E) {
    return w.H.useLayoutEffect(o, E);
  }, Z.useMemo = function(o, E) {
    return w.H.useMemo(o, E);
  }, Z.useOptimistic = function(o, E) {
    return w.H.useOptimistic(o, E);
  }, Z.useReducer = function(o, E, j) {
    return w.H.useReducer(o, E, j);
  }, Z.useRef = function(o) {
    return w.H.useRef(o);
  }, Z.useState = function(o) {
    return w.H.useState(o);
  }, Z.useSyncExternalStore = function(o, E, j) {
    return w.H.useSyncExternalStore(
      o,
      E,
      j
    );
  }, Z.useTransition = function() {
    return w.H.useTransition();
  }, Z.version = "19.2.8", Z;
}
var Tm;
function bf() {
  return Tm || (Tm = 1, of.exports = Pv()), of.exports;
}
var Ul = bf(), mf = { exports: {} }, Du = {}, hf = { exports: {} }, vf = {};
var Am;
function l0() {
  return Am || (Am = 1, (function(N) {
    function Rl(b, _) {
      var Y = b.length;
      b.push(_);
      l: for (; 0 < Y; ) {
        var K = Y - 1 >>> 1, P = b[K];
        if (0 < gl(P, _))
          b[K] = _, b[Y] = P, Y = K;
        else break l;
      }
    }
    function cl(b) {
      return b.length === 0 ? null : b[0];
    }
    function g(b) {
      if (b.length === 0) return null;
      var _ = b[0], Y = b.pop();
      if (Y !== _) {
        b[0] = Y;
        l: for (var K = 0, P = b.length, o = P >>> 1; K < o; ) {
          var E = 2 * (K + 1) - 1, j = b[E], O = E + 1, Q = b[O];
          if (0 > gl(j, Y))
            O < P && 0 > gl(Q, j) ? (b[K] = Q, b[O] = Y, K = O) : (b[K] = j, b[E] = Y, K = E);
          else if (O < P && 0 > gl(Q, Y))
            b[K] = Q, b[O] = Y, K = O;
          else break l;
        }
      }
      return _;
    }
    function gl(b, _) {
      var Y = b.sortIndex - _.sortIndex;
      return Y !== 0 ? Y : b.id - _.id;
    }
    if (N.unstable_now = void 0, typeof performance == "object" && typeof performance.now == "function") {
      var G = performance;
      N.unstable_now = function() {
        return G.now();
      };
    } else {
      var _l = Date, hl = _l.now();
      N.unstable_now = function() {
        return _l.now() - hl;
      };
    }
    var C = [], p = [], X = 1, R = null, vl = 3, Al = !1, Ql = !1, Cl = !1, rt = !1, Yl = typeof setTimeout == "function" ? setTimeout : null, Ct = typeof clearTimeout == "function" ? clearTimeout : null, Bl = typeof setImmediate < "u" ? setImmediate : null;
    function Zl(b) {
      for (var _ = cl(p); _ !== null; ) {
        if (_.callback === null) g(p);
        else if (_.startTime <= b)
          g(p), _.sortIndex = _.expirationTime, Rl(C, _);
        else break;
        _ = cl(p);
      }
    }
    function Sl(b) {
      if (Cl = !1, Zl(b), !Ql)
        if (cl(C) !== null)
          Ql = !0, bl || (bl = !0, zl());
        else {
          var _ = cl(p);
          _ !== null && Pl(Sl, _.startTime - b);
        }
    }
    var bl = !1, w = -1, Ll = 5, kl = -1;
    function Qt() {
      return rt ? !0 : !(N.unstable_now() - kl < Ll);
    }
    function gt() {
      if (rt = !1, bl) {
        var b = N.unstable_now();
        kl = b;
        var _ = !0;
        try {
          l: {
            Ql = !1, Cl && (Cl = !1, Ct(w), w = -1), Al = !0;
            var Y = vl;
            try {
              t: {
                for (Zl(b), R = cl(C); R !== null && !(R.expirationTime > b && Qt()); ) {
                  var K = R.callback;
                  if (typeof K == "function") {
                    R.callback = null, vl = R.priorityLevel;
                    var P = K(
                      R.expirationTime <= b
                    );
                    if (b = N.unstable_now(), typeof P == "function") {
                      R.callback = P, Zl(b), _ = !0;
                      break t;
                    }
                    R === cl(C) && g(C), Zl(b);
                  } else g(C);
                  R = cl(C);
                }
                if (R !== null) _ = !0;
                else {
                  var o = cl(p);
                  o !== null && Pl(
                    Sl,
                    o.startTime - b
                  ), _ = !1;
                }
              }
              break l;
            } finally {
              R = null, vl = Y, Al = !1;
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
      var Ht = new MessageChannel(), St = Ht.port2;
      Ht.port1.onmessage = gt, zl = function() {
        St.postMessage(null);
      };
    } else
      zl = function() {
        Yl(gt, 0);
      };
    function Pl(b, _) {
      w = Yl(function() {
        b(N.unstable_now());
      }, _);
    }
    N.unstable_IdlePriority = 5, N.unstable_ImmediatePriority = 1, N.unstable_LowPriority = 4, N.unstable_NormalPriority = 3, N.unstable_Profiling = null, N.unstable_UserBlockingPriority = 2, N.unstable_cancelCallback = function(b) {
      b.callback = null;
    }, N.unstable_forceFrameRate = function(b) {
      0 > b || 125 < b ? console.error(
        "forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"
      ) : Ll = 0 < b ? Math.floor(1e3 / b) : 5;
    }, N.unstable_getCurrentPriorityLevel = function() {
      return vl;
    }, N.unstable_next = function(b) {
      switch (vl) {
        case 1:
        case 2:
        case 3:
          var _ = 3;
          break;
        default:
          _ = vl;
      }
      var Y = vl;
      vl = _;
      try {
        return b();
      } finally {
        vl = Y;
      }
    }, N.unstable_requestPaint = function() {
      rt = !0;
    }, N.unstable_runWithPriority = function(b, _) {
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
      var Y = vl;
      vl = b;
      try {
        return _();
      } finally {
        vl = Y;
      }
    }, N.unstable_scheduleCallback = function(b, _, Y) {
      var K = N.unstable_now();
      switch (typeof Y == "object" && Y !== null ? (Y = Y.delay, Y = typeof Y == "number" && 0 < Y ? K + Y : K) : Y = K, b) {
        case 1:
          var P = -1;
          break;
        case 2:
          P = 250;
          break;
        case 5:
          P = 1073741823;
          break;
        case 4:
          P = 1e4;
          break;
        default:
          P = 5e3;
      }
      return P = Y + P, b = {
        id: X++,
        callback: _,
        priorityLevel: b,
        startTime: Y,
        expirationTime: P,
        sortIndex: -1
      }, Y > K ? (b.sortIndex = Y, Rl(p, b), cl(C) === null && b === cl(p) && (Cl ? (Ct(w), w = -1) : Cl = !0, Pl(Sl, Y - K))) : (b.sortIndex = P, Rl(C, b), Ql || Al || (Ql = !0, bl || (bl = !0, zl()))), b;
    }, N.unstable_shouldYield = Qt, N.unstable_wrapCallback = function(b) {
      var _ = vl;
      return function() {
        var Y = vl;
        vl = _;
        try {
          return b.apply(this, arguments);
        } finally {
          vl = Y;
        }
      };
    };
  })(vf)), vf;
}
var xm;
function t0() {
  return xm || (xm = 1, hf.exports = l0()), hf.exports;
}
var yf = { exports: {} }, $l = {};
var jm;
function a0() {
  if (jm) return $l;
  jm = 1;
  var N = bf();
  function Rl(C) {
    var p = "https://react.dev/errors/" + C;
    if (1 < arguments.length) {
      p += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var X = 2; X < arguments.length; X++)
        p += "&args[]=" + encodeURIComponent(arguments[X]);
    }
    return "Minified React error #" + C + "; visit " + p + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function cl() {
  }
  var g = {
    d: {
      f: cl,
      r: function() {
        throw Error(Rl(522));
      },
      D: cl,
      C: cl,
      L: cl,
      m: cl,
      X: cl,
      S: cl,
      M: cl
    },
    p: 0,
    findDOMNode: null
  }, gl = /* @__PURE__ */ Symbol.for("react.portal");
  function G(C, p, X) {
    var R = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
    return {
      $$typeof: gl,
      key: R == null ? null : "" + R,
      children: C,
      containerInfo: p,
      implementation: X
    };
  }
  var _l = N.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
  function hl(C, p) {
    if (C === "font") return "";
    if (typeof p == "string")
      return p === "use-credentials" ? p : "";
  }
  return $l.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = g, $l.createPortal = function(C, p) {
    var X = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
    if (!p || p.nodeType !== 1 && p.nodeType !== 9 && p.nodeType !== 11)
      throw Error(Rl(299));
    return G(C, p, null, X);
  }, $l.flushSync = function(C) {
    var p = _l.T, X = g.p;
    try {
      if (_l.T = null, g.p = 2, C) return C();
    } finally {
      _l.T = p, g.p = X, g.d.f();
    }
  }, $l.preconnect = function(C, p) {
    typeof C == "string" && (p ? (p = p.crossOrigin, p = typeof p == "string" ? p === "use-credentials" ? p : "" : void 0) : p = null, g.d.C(C, p));
  }, $l.prefetchDNS = function(C) {
    typeof C == "string" && g.d.D(C);
  }, $l.preinit = function(C, p) {
    if (typeof C == "string" && p && typeof p.as == "string") {
      var X = p.as, R = hl(X, p.crossOrigin), vl = typeof p.integrity == "string" ? p.integrity : void 0, Al = typeof p.fetchPriority == "string" ? p.fetchPriority : void 0;
      X === "style" ? g.d.S(
        C,
        typeof p.precedence == "string" ? p.precedence : void 0,
        {
          crossOrigin: R,
          integrity: vl,
          fetchPriority: Al
        }
      ) : X === "script" && g.d.X(C, {
        crossOrigin: R,
        integrity: vl,
        fetchPriority: Al,
        nonce: typeof p.nonce == "string" ? p.nonce : void 0
      });
    }
  }, $l.preinitModule = function(C, p) {
    if (typeof C == "string")
      if (typeof p == "object" && p !== null) {
        if (p.as == null || p.as === "script") {
          var X = hl(
            p.as,
            p.crossOrigin
          );
          g.d.M(C, {
            crossOrigin: X,
            integrity: typeof p.integrity == "string" ? p.integrity : void 0,
            nonce: typeof p.nonce == "string" ? p.nonce : void 0
          });
        }
      } else p == null && g.d.M(C);
  }, $l.preload = function(C, p) {
    if (typeof C == "string" && typeof p == "object" && p !== null && typeof p.as == "string") {
      var X = p.as, R = hl(X, p.crossOrigin);
      g.d.L(C, X, {
        crossOrigin: R,
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
  }, $l.preloadModule = function(C, p) {
    if (typeof C == "string")
      if (p) {
        var X = hl(p.as, p.crossOrigin);
        g.d.m(C, {
          as: typeof p.as == "string" && p.as !== "script" ? p.as : void 0,
          crossOrigin: X,
          integrity: typeof p.integrity == "string" ? p.integrity : void 0
        });
      } else g.d.m(C);
  }, $l.requestFormReset = function(C) {
    g.d.r(C);
  }, $l.unstable_batchedUpdates = function(C, p) {
    return C(p);
  }, $l.useFormState = function(C, p, X) {
    return _l.H.useFormState(C, p, X);
  }, $l.useFormStatus = function() {
    return _l.H.useHostTransitionStatus();
  }, $l.version = "19.2.8", $l;
}
var _m;
function e0() {
  if (_m) return yf.exports;
  _m = 1;
  function N() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function"))
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(N);
      } catch (Rl) {
        console.error(Rl);
      }
  }
  return N(), yf.exports = a0(), yf.exports;
}
var Mm;
function u0() {
  if (Mm) return Du;
  Mm = 1;
  var N = t0(), Rl = bf(), cl = e0();
  function g(l) {
    var t = "https://react.dev/errors/" + l;
    if (1 < arguments.length) {
      t += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var a = 2; a < arguments.length; a++)
        t += "&args[]=" + encodeURIComponent(arguments[a]);
    }
    return "Minified React error #" + l + "; visit " + t + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function gl(l) {
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
  function _l(l) {
    if (l.tag === 13) {
      var t = l.memoizedState;
      if (t === null && (l = l.alternate, l !== null && (t = l.memoizedState)), t !== null) return t.dehydrated;
    }
    return null;
  }
  function hl(l) {
    if (l.tag === 31) {
      var t = l.memoizedState;
      if (t === null && (l = l.alternate, l !== null && (t = l.memoizedState)), t !== null) return t.dehydrated;
    }
    return null;
  }
  function C(l) {
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
          if (n === a) return C(u), l;
          if (n === e) return C(u), t;
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
  var R = Object.assign, vl = /* @__PURE__ */ Symbol.for("react.element"), Al = /* @__PURE__ */ Symbol.for("react.transitional.element"), Ql = /* @__PURE__ */ Symbol.for("react.portal"), Cl = /* @__PURE__ */ Symbol.for("react.fragment"), rt = /* @__PURE__ */ Symbol.for("react.strict_mode"), Yl = /* @__PURE__ */ Symbol.for("react.profiler"), Ct = /* @__PURE__ */ Symbol.for("react.consumer"), Bl = /* @__PURE__ */ Symbol.for("react.context"), Zl = /* @__PURE__ */ Symbol.for("react.forward_ref"), Sl = /* @__PURE__ */ Symbol.for("react.suspense"), bl = /* @__PURE__ */ Symbol.for("react.suspense_list"), w = /* @__PURE__ */ Symbol.for("react.memo"), Ll = /* @__PURE__ */ Symbol.for("react.lazy"), kl = /* @__PURE__ */ Symbol.for("react.activity"), Qt = /* @__PURE__ */ Symbol.for("react.memo_cache_sentinel"), gt = Symbol.iterator;
  function zl(l) {
    return l === null || typeof l != "object" ? null : (l = gt && l[gt] || l["@@iterator"], typeof l == "function" ? l : null);
  }
  var Ht = /* @__PURE__ */ Symbol.for("react.client.reference");
  function St(l) {
    if (l == null) return null;
    if (typeof l == "function")
      return l.$$typeof === Ht ? null : l.displayName || l.name || null;
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
        case Ql:
          return "Portal";
        case Bl:
          return l.displayName || "Context";
        case Ct:
          return (l._context.displayName || "Context") + ".Consumer";
        case Zl:
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
  var Pl = Array.isArray, b = Rl.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, _ = cl.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, Y = {
    pending: !1,
    data: null,
    method: null,
    action: null
  }, K = [], P = -1;
  function o(l) {
    return { current: l };
  }
  function E(l) {
    0 > P || (l.current = K[P], K[P] = null, P--);
  }
  function j(l, t) {
    P++, K[P] = l.current, l.current = t;
  }
  var O = o(null), Q = o(null), J = o(null), ll = o(null);
  function Hl(l, t) {
    switch (j(J, t), j(Q, l), j(O, null), t.nodeType) {
      case 9:
      case 11:
        l = (l = t.documentElement) && (l = l.namespaceURI) ? Lo(l) : 0;
        break;
      default:
        if (l = t.tagName, t = t.namespaceURI)
          t = Lo(t), l = Vo(t, l);
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
    E(O), E(Q), E(J);
  }
  function qt(l) {
    l.memoizedState !== null && j(ll, l);
    var t = O.current, a = Vo(t, l.type);
    t !== a && (j(Q, l), j(O, a));
  }
  function bt(l) {
    Q.current === l && (E(O), E(Q)), ll.current === l && (E(ll), ju._currentValue = Y);
  }
  var Da, Uu;
  function Ot(l) {
    if (Da === void 0)
      try {
        throw Error();
      } catch (a) {
        var t = a.stack.trim().match(/\n( *(at )?)/);
        Da = t && t[1] || "", Uu = -1 < a.stack.indexOf(`
    at`) ? " (<anonymous>)" : -1 < a.stack.indexOf("@") ? "@unknown:0:0" : "";
      }
    return `
` + Da + l + Uu;
  }
  var Fa = !1;
  function d(l, t) {
    if (!l || Fa) return "";
    Fa = !0;
    var a = Error.prepareStackTrace;
    Error.prepareStackTrace = void 0;
    try {
      var e = {
        DetermineComponentFrameRoot: function() {
          try {
            if (t) {
              var A = function() {
                throw Error();
              };
              if (Object.defineProperty(A.prototype, "props", {
                set: function() {
                  throw Error();
                }
              }), typeof Reflect == "object" && Reflect.construct) {
                try {
                  Reflect.construct(A, []);
                } catch (S) {
                  var r = S;
                }
                Reflect.construct(l, [], A);
              } else {
                try {
                  A.call();
                } catch (S) {
                  r = S;
                }
                l.call(A.prototype);
              }
            } else {
              try {
                throw Error();
              } catch (S) {
                r = S;
              }
              (A = l()) && typeof A.catch == "function" && A.catch(function() {
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
      Fa = !1, Error.prepareStackTrace = a;
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
  function M(l) {
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
  var q = Object.prototype.hasOwnProperty, el = N.unstable_scheduleCallback, Ua = N.unstable_cancelCallback, qe = N.unstable_shouldYield, Ye = N.unstable_requestPaint, Fl = N.unstable_now, Dm = N.unstable_getCurrentPriorityLevel, pf = N.unstable_ImmediatePriority, zf = N.unstable_UserBlockingPriority, Ru = N.unstable_NormalPriority, Um = N.unstable_LowPriority, Ef = N.unstable_IdlePriority, Rm = N.log, Cm = N.unstable_setDisableYieldValue, Be = null, ct = null;
  function ia(l) {
    if (typeof Rm == "function" && Cm(l), ct && typeof ct.setStrictMode == "function")
      try {
        ct.setStrictMode(Be, l);
      } catch {
      }
  }
  var ft = Math.clz32 ? Math.clz32 : Ym, Hm = Math.log, qm = Math.LN2;
  function Ym(l) {
    return l >>>= 0, l === 0 ? 32 : 31 - (Hm(l) / qm | 0) | 0;
  }
  var Cu = 256, Hu = 262144, qu = 4194304;
  function Ra(l) {
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
  function Yu(l, t, a) {
    var e = l.pendingLanes;
    if (e === 0) return 0;
    var u = 0, n = l.suspendedLanes, i = l.pingedLanes;
    l = l.warmLanes;
    var c = e & 134217727;
    return c !== 0 ? (e = c & ~n, e !== 0 ? u = Ra(e) : (i &= c, i !== 0 ? u = Ra(i) : a || (a = c & ~l, a !== 0 && (u = Ra(a))))) : (c = e & ~n, c !== 0 ? u = Ra(c) : i !== 0 ? u = Ra(i) : a || (a = e & ~l, a !== 0 && (u = Ra(a)))), u === 0 ? 0 : t !== 0 && t !== u && (t & n) === 0 && (n = u & -u, a = t & -t, n >= a || n === 32 && (a & 4194048) !== 0) ? t : u;
  }
  function Ge(l, t) {
    return (l.pendingLanes & ~(l.suspendedLanes & ~l.pingedLanes) & t) === 0;
  }
  function Bm(l, t) {
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
  function Tf() {
    var l = qu;
    return qu <<= 1, (qu & 62914560) === 0 && (qu = 4194304), l;
  }
  function Fn(l) {
    for (var t = [], a = 0; 31 > a; a++) t.push(l);
    return t;
  }
  function Xe(l, t) {
    l.pendingLanes |= t, t !== 268435456 && (l.suspendedLanes = 0, l.pingedLanes = 0, l.warmLanes = 0);
  }
  function Gm(l, t, a, e, u, n) {
    var i = l.pendingLanes;
    l.pendingLanes = a, l.suspendedLanes = 0, l.pingedLanes = 0, l.warmLanes = 0, l.expiredLanes &= a, l.entangledLanes &= a, l.errorRecoveryDisabledLanes &= a, l.shellSuspendCounter = 0;
    var c = l.entanglements, s = l.expirationTimes, y = l.hiddenUpdates;
    for (a = i & ~a; 0 < a; ) {
      var z = 31 - ft(a), A = 1 << z;
      c[z] = 0, s[z] = -1;
      var r = y[z];
      if (r !== null)
        for (y[z] = null, z = 0; z < r.length; z++) {
          var S = r[z];
          S !== null && (S.lane &= -536870913);
        }
      a &= ~A;
    }
    e !== 0 && Af(l, e, 0), n !== 0 && u === 0 && l.tag !== 0 && (l.suspendedLanes |= n & ~(i & ~t));
  }
  function Af(l, t, a) {
    l.pendingLanes |= t, l.suspendedLanes &= ~t;
    var e = 31 - ft(t);
    l.entangledLanes |= t, l.entanglements[e] = l.entanglements[e] | 1073741824 | a & 261930;
  }
  function xf(l, t) {
    var a = l.entangledLanes |= t;
    for (l = l.entanglements; a; ) {
      var e = 31 - ft(a), u = 1 << e;
      u & t | l[e] & t && (l[e] |= t), a &= ~u;
    }
  }
  function jf(l, t) {
    var a = t & -t;
    return a = (a & 42) !== 0 ? 1 : In(a), (a & (l.suspendedLanes | t)) !== 0 ? 0 : a;
  }
  function In(l) {
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
  function Pn(l) {
    return l &= -l, 2 < l ? 8 < l ? (l & 134217727) !== 0 ? 32 : 268435456 : 8 : 2;
  }
  function _f() {
    var l = _.p;
    return l !== 0 ? l : (l = window.event, l === void 0 ? 32 : hm(l.type));
  }
  function Mf(l, t) {
    var a = _.p;
    try {
      return _.p = l, t();
    } finally {
      _.p = a;
    }
  }
  var ca = Math.random().toString(36).slice(2), Vl = "__reactFiber$" + ca, lt = "__reactProps$" + ca, Ia = "__reactContainer$" + ca, li = "__reactEvents$" + ca, Xm = "__reactListeners$" + ca, Qm = "__reactHandles$" + ca, Nf = "__reactResources$" + ca, Qe = "__reactMarker$" + ca;
  function ti(l) {
    delete l[Vl], delete l[lt], delete l[li], delete l[Xm], delete l[Qm];
  }
  function Pa(l) {
    var t = l[Vl];
    if (t) return t;
    for (var a = l.parentNode; a; ) {
      if (t = a[Ia] || a[Vl]) {
        if (a = t.alternate, t.child !== null || a !== null && a.child !== null)
          for (l = Fo(l); l !== null; ) {
            if (a = l[Vl]) return a;
            l = Fo(l);
          }
        return t;
      }
      l = a, a = l.parentNode;
    }
    return null;
  }
  function le(l) {
    if (l = l[Vl] || l[Ia]) {
      var t = l.tag;
      if (t === 5 || t === 6 || t === 13 || t === 31 || t === 26 || t === 27 || t === 3)
        return l;
    }
    return null;
  }
  function Ze(l) {
    var t = l.tag;
    if (t === 5 || t === 26 || t === 27 || t === 6) return l.stateNode;
    throw Error(g(33));
  }
  function te(l) {
    var t = l[Nf];
    return t || (t = l[Nf] = { hoistableStyles: /* @__PURE__ */ new Map(), hoistableScripts: /* @__PURE__ */ new Map() }), t;
  }
  function Gl(l) {
    l[Qe] = !0;
  }
  var Of = /* @__PURE__ */ new Set(), Df = {};
  function Ca(l, t) {
    ae(l, t), ae(l + "Capture", t);
  }
  function ae(l, t) {
    for (Df[l] = t, l = 0; l < t.length; l++)
      Of.add(t[l]);
  }
  var Zm = RegExp(
    "^[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"
  ), Uf = {}, Rf = {};
  function Lm(l) {
    return q.call(Rf, l) ? !0 : q.call(Uf, l) ? !1 : Zm.test(l) ? Rf[l] = !0 : (Uf[l] = !0, !1);
  }
  function Bu(l, t, a) {
    if (Lm(t))
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
  function Gu(l, t, a) {
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
  function Zt(l, t, a, e) {
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
  function Cf(l) {
    var t = l.type;
    return (l = l.nodeName) && l.toLowerCase() === "input" && (t === "checkbox" || t === "radio");
  }
  function Vm(l, t, a) {
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
  function ai(l) {
    if (!l._valueTracker) {
      var t = Cf(l) ? "checked" : "value";
      l._valueTracker = Vm(
        l,
        t,
        "" + l[t]
      );
    }
  }
  function Hf(l) {
    if (!l) return !1;
    var t = l._valueTracker;
    if (!t) return !0;
    var a = t.getValue(), e = "";
    return l && (e = Cf(l) ? l.checked ? "true" : "false" : l.value), l = e, l !== a ? (t.setValue(l), !0) : !1;
  }
  function Xu(l) {
    if (l = l || (typeof document < "u" ? document : void 0), typeof l > "u") return null;
    try {
      return l.activeElement || l.body;
    } catch {
      return l.body;
    }
  }
  var Km = /[\n"\\]/g;
  function zt(l) {
    return l.replace(
      Km,
      function(t) {
        return "\\" + t.charCodeAt(0).toString(16) + " ";
      }
    );
  }
  function ei(l, t, a, e, u, n, i, c) {
    l.name = "", i != null && typeof i != "function" && typeof i != "symbol" && typeof i != "boolean" ? l.type = i : l.removeAttribute("type"), t != null ? i === "number" ? (t === 0 && l.value === "" || l.value != t) && (l.value = "" + pt(t)) : l.value !== "" + pt(t) && (l.value = "" + pt(t)) : i !== "submit" && i !== "reset" || l.removeAttribute("value"), t != null ? ui(l, i, pt(t)) : a != null ? ui(l, i, pt(a)) : e != null && l.removeAttribute("value"), u == null && n != null && (l.defaultChecked = !!n), u != null && (l.checked = u && typeof u != "function" && typeof u != "symbol"), c != null && typeof c != "function" && typeof c != "symbol" && typeof c != "boolean" ? l.name = "" + pt(c) : l.removeAttribute("name");
  }
  function qf(l, t, a, e, u, n, i, c) {
    if (n != null && typeof n != "function" && typeof n != "symbol" && typeof n != "boolean" && (l.type = n), t != null || a != null) {
      if (!(n !== "submit" && n !== "reset" || t != null)) {
        ai(l);
        return;
      }
      a = a != null ? "" + pt(a) : "", t = t != null ? "" + pt(t) : a, c || t === l.value || (l.value = t), l.defaultValue = t;
    }
    e = e ?? u, e = typeof e != "function" && typeof e != "symbol" && !!e, l.checked = c ? l.checked : !!e, l.defaultChecked = !!e, i != null && typeof i != "function" && typeof i != "symbol" && typeof i != "boolean" && (l.name = i), ai(l);
  }
  function ui(l, t, a) {
    t === "number" && Xu(l.ownerDocument) === l || l.defaultValue === "" + a || (l.defaultValue = "" + a);
  }
  function ee(l, t, a, e) {
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
  function Yf(l, t, a) {
    if (t != null && (t = "" + pt(t), t !== l.value && (l.value = t), a == null)) {
      l.defaultValue !== t && (l.defaultValue = t);
      return;
    }
    l.defaultValue = a != null ? "" + pt(a) : "";
  }
  function Bf(l, t, a, e) {
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
    a = pt(t), l.defaultValue = a, e = l.textContent, e === a && e !== "" && e !== null && (l.value = e), ai(l);
  }
  function ue(l, t) {
    if (t) {
      var a = l.firstChild;
      if (a && a === l.lastChild && a.nodeType === 3) {
        a.nodeValue = t;
        return;
      }
    }
    l.textContent = t;
  }
  var Jm = new Set(
    "animationIterationCount aspectRatio borderImageOutset borderImageSlice borderImageWidth boxFlex boxFlexGroup boxOrdinalGroup columnCount columns flex flexGrow flexPositive flexShrink flexNegative flexOrder gridArea gridRow gridRowEnd gridRowSpan gridRowStart gridColumn gridColumnEnd gridColumnSpan gridColumnStart fontWeight lineClamp lineHeight opacity order orphans scale tabSize widows zIndex zoom fillOpacity floodOpacity stopOpacity strokeDasharray strokeDashoffset strokeMiterlimit strokeOpacity strokeWidth MozAnimationIterationCount MozBoxFlex MozBoxFlexGroup MozLineClamp msAnimationIterationCount msFlex msZoom msFlexGrow msFlexNegative msFlexOrder msFlexPositive msFlexShrink msGridColumn msGridColumnSpan msGridRow msGridRowSpan WebkitAnimationIterationCount WebkitBoxFlex WebKitBoxFlexGroup WebkitBoxOrdinalGroup WebkitColumnCount WebkitColumns WebkitFlex WebkitFlexGrow WebkitFlexPositive WebkitFlexShrink WebkitLineClamp".split(
      " "
    )
  );
  function Gf(l, t, a) {
    var e = t.indexOf("--") === 0;
    a == null || typeof a == "boolean" || a === "" ? e ? l.setProperty(t, "") : t === "float" ? l.cssFloat = "" : l[t] = "" : e ? l.setProperty(t, a) : typeof a != "number" || a === 0 || Jm.has(t) ? t === "float" ? l.cssFloat = a : l[t] = ("" + a).trim() : l[t] = a + "px";
  }
  function Xf(l, t, a) {
    if (t != null && typeof t != "object")
      throw Error(g(62));
    if (l = l.style, a != null) {
      for (var e in a)
        !a.hasOwnProperty(e) || t != null && t.hasOwnProperty(e) || (e.indexOf("--") === 0 ? l.setProperty(e, "") : e === "float" ? l.cssFloat = "" : l[e] = "");
      for (var u in t)
        e = t[u], t.hasOwnProperty(u) && a[u] !== e && Gf(l, u, e);
    } else
      for (var n in t)
        t.hasOwnProperty(n) && Gf(l, n, t[n]);
  }
  function ni(l) {
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
  var wm = /* @__PURE__ */ new Map([
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
  ]), Wm = /^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*:/i;
  function Qu(l) {
    return Wm.test("" + l) ? "javascript:throw new Error('React has blocked a javascript: URL as a security precaution.')" : l;
  }
  function Lt() {
  }
  var ii = null;
  function ci(l) {
    return l = l.target || l.srcElement || window, l.correspondingUseElement && (l = l.correspondingUseElement), l.nodeType === 3 ? l.parentNode : l;
  }
  var ne = null, ie = null;
  function Qf(l) {
    var t = le(l);
    if (t && (l = t.stateNode)) {
      var a = l[lt] || null;
      l: switch (l = t.stateNode, t.type) {
        case "input":
          if (ei(
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
                ei(
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
              e = a[t], e.form === l.form && Hf(e);
          }
          break l;
        case "textarea":
          Yf(l, a.value, a.defaultValue);
          break l;
        case "select":
          t = a.value, t != null && ee(l, !!a.multiple, t, !1);
      }
    }
  }
  var fi = !1;
  function Zf(l, t, a) {
    if (fi) return l(t, a);
    fi = !0;
    try {
      var e = l(t);
      return e;
    } finally {
      if (fi = !1, (ne !== null || ie !== null) && (Nn(), ne && (t = ne, l = ie, ie = ne = null, Qf(t), l)))
        for (t = 0; t < l.length; t++) Qf(l[t]);
    }
  }
  function Le(l, t) {
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
  var Vt = !(typeof window > "u" || typeof window.document > "u" || typeof window.document.createElement > "u"), si = !1;
  if (Vt)
    try {
      var Ve = {};
      Object.defineProperty(Ve, "passive", {
        get: function() {
          si = !0;
        }
      }), window.addEventListener("test", Ve, Ve), window.removeEventListener("test", Ve, Ve);
    } catch {
      si = !1;
    }
  var fa = null, di = null, Zu = null;
  function Lf() {
    if (Zu) return Zu;
    var l, t = di, a = t.length, e, u = "value" in fa ? fa.value : fa.textContent, n = u.length;
    for (l = 0; l < a && t[l] === u[l]; l++) ;
    var i = a - l;
    for (e = 1; e <= i && t[a - e] === u[n - e]; e++) ;
    return Zu = u.slice(l, 1 < e ? 1 - e : void 0);
  }
  function Lu(l) {
    var t = l.keyCode;
    return "charCode" in l ? (l = l.charCode, l === 0 && t === 13 && (l = 13)) : l = t, l === 10 && (l = 13), 32 <= l || l === 13 ? l : 0;
  }
  function Vu() {
    return !0;
  }
  function Vf() {
    return !1;
  }
  function tt(l) {
    function t(a, e, u, n, i) {
      this._reactName = a, this._targetInst = u, this.type = e, this.nativeEvent = n, this.target = i, this.currentTarget = null;
      for (var c in l)
        l.hasOwnProperty(c) && (a = l[c], this[c] = a ? a(n) : n[c]);
      return this.isDefaultPrevented = (n.defaultPrevented != null ? n.defaultPrevented : n.returnValue === !1) ? Vu : Vf, this.isPropagationStopped = Vf, this;
    }
    return R(t.prototype, {
      preventDefault: function() {
        this.defaultPrevented = !0;
        var a = this.nativeEvent;
        a && (a.preventDefault ? a.preventDefault() : typeof a.returnValue != "unknown" && (a.returnValue = !1), this.isDefaultPrevented = Vu);
      },
      stopPropagation: function() {
        var a = this.nativeEvent;
        a && (a.stopPropagation ? a.stopPropagation() : typeof a.cancelBubble != "unknown" && (a.cancelBubble = !0), this.isPropagationStopped = Vu);
      },
      persist: function() {
      },
      isPersistent: Vu
    }), t;
  }
  var Ha = {
    eventPhase: 0,
    bubbles: 0,
    cancelable: 0,
    timeStamp: function(l) {
      return l.timeStamp || Date.now();
    },
    defaultPrevented: 0,
    isTrusted: 0
  }, Ku = tt(Ha), Ke = R({}, Ha, { view: 0, detail: 0 }), $m = tt(Ke), oi, mi, Je, Ju = R({}, Ke, {
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
    getModifierState: vi,
    button: 0,
    buttons: 0,
    relatedTarget: function(l) {
      return l.relatedTarget === void 0 ? l.fromElement === l.srcElement ? l.toElement : l.fromElement : l.relatedTarget;
    },
    movementX: function(l) {
      return "movementX" in l ? l.movementX : (l !== Je && (Je && l.type === "mousemove" ? (oi = l.screenX - Je.screenX, mi = l.screenY - Je.screenY) : mi = oi = 0, Je = l), oi);
    },
    movementY: function(l) {
      return "movementY" in l ? l.movementY : mi;
    }
  }), Kf = tt(Ju), km = R({}, Ju, { dataTransfer: 0 }), Fm = tt(km), Im = R({}, Ke, { relatedTarget: 0 }), hi = tt(Im), Pm = R({}, Ha, {
    animationName: 0,
    elapsedTime: 0,
    pseudoElement: 0
  }), lh = tt(Pm), th = R({}, Ha, {
    clipboardData: function(l) {
      return "clipboardData" in l ? l.clipboardData : window.clipboardData;
    }
  }), ah = tt(th), eh = R({}, Ha, { data: 0 }), Jf = tt(eh), uh = {
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
  }, nh = {
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
  }, ih = {
    Alt: "altKey",
    Control: "ctrlKey",
    Meta: "metaKey",
    Shift: "shiftKey"
  };
  function ch(l) {
    var t = this.nativeEvent;
    return t.getModifierState ? t.getModifierState(l) : (l = ih[l]) ? !!t[l] : !1;
  }
  function vi() {
    return ch;
  }
  var fh = R({}, Ke, {
    key: function(l) {
      if (l.key) {
        var t = uh[l.key] || l.key;
        if (t !== "Unidentified") return t;
      }
      return l.type === "keypress" ? (l = Lu(l), l === 13 ? "Enter" : String.fromCharCode(l)) : l.type === "keydown" || l.type === "keyup" ? nh[l.keyCode] || "Unidentified" : "";
    },
    code: 0,
    location: 0,
    ctrlKey: 0,
    shiftKey: 0,
    altKey: 0,
    metaKey: 0,
    repeat: 0,
    locale: 0,
    getModifierState: vi,
    charCode: function(l) {
      return l.type === "keypress" ? Lu(l) : 0;
    },
    keyCode: function(l) {
      return l.type === "keydown" || l.type === "keyup" ? l.keyCode : 0;
    },
    which: function(l) {
      return l.type === "keypress" ? Lu(l) : l.type === "keydown" || l.type === "keyup" ? l.keyCode : 0;
    }
  }), sh = tt(fh), dh = R({}, Ju, {
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
  }), wf = tt(dh), oh = R({}, Ke, {
    touches: 0,
    targetTouches: 0,
    changedTouches: 0,
    altKey: 0,
    metaKey: 0,
    ctrlKey: 0,
    shiftKey: 0,
    getModifierState: vi
  }), mh = tt(oh), hh = R({}, Ha, {
    propertyName: 0,
    elapsedTime: 0,
    pseudoElement: 0
  }), vh = tt(hh), yh = R({}, Ju, {
    deltaX: function(l) {
      return "deltaX" in l ? l.deltaX : "wheelDeltaX" in l ? -l.wheelDeltaX : 0;
    },
    deltaY: function(l) {
      return "deltaY" in l ? l.deltaY : "wheelDeltaY" in l ? -l.wheelDeltaY : "wheelDelta" in l ? -l.wheelDelta : 0;
    },
    deltaZ: 0,
    deltaMode: 0
  }), rh = tt(yh), gh = R({}, Ha, {
    newState: 0,
    oldState: 0
  }), Sh = tt(gh), bh = [9, 13, 27, 32], yi = Vt && "CompositionEvent" in window, we = null;
  Vt && "documentMode" in document && (we = document.documentMode);
  var ph = Vt && "TextEvent" in window && !we, Wf = Vt && (!yi || we && 8 < we && 11 >= we), $f = " ", kf = !1;
  function Ff(l, t) {
    switch (l) {
      case "keyup":
        return bh.indexOf(t.keyCode) !== -1;
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
  function If(l) {
    return l = l.detail, typeof l == "object" && "data" in l ? l.data : null;
  }
  var ce = !1;
  function zh(l, t) {
    switch (l) {
      case "compositionend":
        return If(t);
      case "keypress":
        return t.which !== 32 ? null : (kf = !0, $f);
      case "textInput":
        return l = t.data, l === $f && kf ? null : l;
      default:
        return null;
    }
  }
  function Eh(l, t) {
    if (ce)
      return l === "compositionend" || !yi && Ff(l, t) ? (l = Lf(), Zu = di = fa = null, ce = !1, l) : null;
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
        return Wf && t.locale !== "ko" ? null : t.data;
      default:
        return null;
    }
  }
  var Th = {
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
  function Pf(l) {
    var t = l && l.nodeName && l.nodeName.toLowerCase();
    return t === "input" ? !!Th[l.type] : t === "textarea";
  }
  function ls(l, t, a, e) {
    ne ? ie ? ie.push(e) : ie = [e] : ne = e, t = qn(t, "onChange"), 0 < t.length && (a = new Ku(
      "onChange",
      "change",
      null,
      a,
      e
    ), l.push({ event: a, listeners: t }));
  }
  var We = null, $e = null;
  function Ah(l) {
    Yo(l, 0);
  }
  function wu(l) {
    var t = Ze(l);
    if (Hf(t)) return l;
  }
  function ts(l, t) {
    if (l === "change") return t;
  }
  var as = !1;
  if (Vt) {
    var ri;
    if (Vt) {
      var gi = "oninput" in document;
      if (!gi) {
        var es = document.createElement("div");
        es.setAttribute("oninput", "return;"), gi = typeof es.oninput == "function";
      }
      ri = gi;
    } else ri = !1;
    as = ri && (!document.documentMode || 9 < document.documentMode);
  }
  function us() {
    We && (We.detachEvent("onpropertychange", ns), $e = We = null);
  }
  function ns(l) {
    if (l.propertyName === "value" && wu($e)) {
      var t = [];
      ls(
        t,
        $e,
        l,
        ci(l)
      ), Zf(Ah, t);
    }
  }
  function xh(l, t, a) {
    l === "focusin" ? (us(), We = t, $e = a, We.attachEvent("onpropertychange", ns)) : l === "focusout" && us();
  }
  function jh(l) {
    if (l === "selectionchange" || l === "keyup" || l === "keydown")
      return wu($e);
  }
  function _h(l, t) {
    if (l === "click") return wu(t);
  }
  function Mh(l, t) {
    if (l === "input" || l === "change")
      return wu(t);
  }
  function Nh(l, t) {
    return l === t && (l !== 0 || 1 / l === 1 / t) || l !== l && t !== t;
  }
  var st = typeof Object.is == "function" ? Object.is : Nh;
  function ke(l, t) {
    if (st(l, t)) return !0;
    if (typeof l != "object" || l === null || typeof t != "object" || t === null)
      return !1;
    var a = Object.keys(l), e = Object.keys(t);
    if (a.length !== e.length) return !1;
    for (e = 0; e < a.length; e++) {
      var u = a[e];
      if (!q.call(t, u) || !st(l[u], t[u]))
        return !1;
    }
    return !0;
  }
  function is(l) {
    for (; l && l.firstChild; ) l = l.firstChild;
    return l;
  }
  function cs(l, t) {
    var a = is(l);
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
      a = is(a);
    }
  }
  function fs(l, t) {
    return l && t ? l === t ? !0 : l && l.nodeType === 3 ? !1 : t && t.nodeType === 3 ? fs(l, t.parentNode) : "contains" in l ? l.contains(t) : l.compareDocumentPosition ? !!(l.compareDocumentPosition(t) & 16) : !1 : !1;
  }
  function ss(l) {
    l = l != null && l.ownerDocument != null && l.ownerDocument.defaultView != null ? l.ownerDocument.defaultView : window;
    for (var t = Xu(l.document); t instanceof l.HTMLIFrameElement; ) {
      try {
        var a = typeof t.contentWindow.location.href == "string";
      } catch {
        a = !1;
      }
      if (a) l = t.contentWindow;
      else break;
      t = Xu(l.document);
    }
    return t;
  }
  function Si(l) {
    var t = l && l.nodeName && l.nodeName.toLowerCase();
    return t && (t === "input" && (l.type === "text" || l.type === "search" || l.type === "tel" || l.type === "url" || l.type === "password") || t === "textarea" || l.contentEditable === "true");
  }
  var Oh = Vt && "documentMode" in document && 11 >= document.documentMode, fe = null, bi = null, Fe = null, pi = !1;
  function ds(l, t, a) {
    var e = a.window === a ? a.document : a.nodeType === 9 ? a : a.ownerDocument;
    pi || fe == null || fe !== Xu(e) || (e = fe, "selectionStart" in e && Si(e) ? e = { start: e.selectionStart, end: e.selectionEnd } : (e = (e.ownerDocument && e.ownerDocument.defaultView || window).getSelection(), e = {
      anchorNode: e.anchorNode,
      anchorOffset: e.anchorOffset,
      focusNode: e.focusNode,
      focusOffset: e.focusOffset
    }), Fe && ke(Fe, e) || (Fe = e, e = qn(bi, "onSelect"), 0 < e.length && (t = new Ku(
      "onSelect",
      "select",
      null,
      t,
      a
    ), l.push({ event: t, listeners: e }), t.target = fe)));
  }
  function qa(l, t) {
    var a = {};
    return a[l.toLowerCase()] = t.toLowerCase(), a["Webkit" + l] = "webkit" + t, a["Moz" + l] = "moz" + t, a;
  }
  var se = {
    animationend: qa("Animation", "AnimationEnd"),
    animationiteration: qa("Animation", "AnimationIteration"),
    animationstart: qa("Animation", "AnimationStart"),
    transitionrun: qa("Transition", "TransitionRun"),
    transitionstart: qa("Transition", "TransitionStart"),
    transitioncancel: qa("Transition", "TransitionCancel"),
    transitionend: qa("Transition", "TransitionEnd")
  }, zi = {}, os = {};
  Vt && (os = document.createElement("div").style, "AnimationEvent" in window || (delete se.animationend.animation, delete se.animationiteration.animation, delete se.animationstart.animation), "TransitionEvent" in window || delete se.transitionend.transition);
  function Ya(l) {
    if (zi[l]) return zi[l];
    if (!se[l]) return l;
    var t = se[l], a;
    for (a in t)
      if (t.hasOwnProperty(a) && a in os)
        return zi[l] = t[a];
    return l;
  }
  var ms = Ya("animationend"), hs = Ya("animationiteration"), vs = Ya("animationstart"), Dh = Ya("transitionrun"), Uh = Ya("transitionstart"), Rh = Ya("transitioncancel"), ys = Ya("transitionend"), rs = /* @__PURE__ */ new Map(), Ei = "abort auxClick beforeToggle cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(
    " "
  );
  Ei.push("scrollEnd");
  function Dt(l, t) {
    rs.set(l, t), Ca(t, [l]);
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
  }, Et = [], de = 0, Ti = 0;
  function $u() {
    for (var l = de, t = Ti = de = 0; t < l; ) {
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
      n !== 0 && gs(a, u, n);
    }
  }
  function ku(l, t, a, e) {
    Et[de++] = l, Et[de++] = t, Et[de++] = a, Et[de++] = e, Ti |= e, l.lanes |= e, l = l.alternate, l !== null && (l.lanes |= e);
  }
  function Ai(l, t, a, e) {
    return ku(l, t, a, e), Fu(l);
  }
  function Ba(l, t) {
    return ku(l, null, null, t), Fu(l);
  }
  function gs(l, t, a) {
    l.lanes |= a;
    var e = l.alternate;
    e !== null && (e.lanes |= a);
    for (var u = !1, n = l.return; n !== null; )
      n.childLanes |= a, e = n.alternate, e !== null && (e.childLanes |= a), n.tag === 22 && (l = n.stateNode, l === null || l._visibility & 1 || (u = !0)), l = n, n = n.return;
    return l.tag === 3 ? (n = l.stateNode, u && t !== null && (u = 31 - ft(a), l = n.hiddenUpdates, e = l[u], e === null ? l[u] = [t] : e.push(t), t.lane = a | 536870912), n) : null;
  }
  function Fu(l) {
    if (50 < bu)
      throw bu = 0, Rc = null, Error(g(185));
    for (var t = l.return; t !== null; )
      l = t, t = l.return;
    return l.tag === 3 ? l.stateNode : null;
  }
  var oe = {};
  function Ch(l, t, a, e) {
    this.tag = l, this.key = a, this.sibling = this.child = this.return = this.stateNode = this.type = this.elementType = null, this.index = 0, this.refCleanup = this.ref = null, this.pendingProps = t, this.dependencies = this.memoizedState = this.updateQueue = this.memoizedProps = null, this.mode = e, this.subtreeFlags = this.flags = 0, this.deletions = null, this.childLanes = this.lanes = 0, this.alternate = null;
  }
  function dt(l, t, a, e) {
    return new Ch(l, t, a, e);
  }
  function xi(l) {
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
  function Ss(l, t) {
    l.flags &= 65011714;
    var a = l.alternate;
    return a === null ? (l.childLanes = 0, l.lanes = t, l.child = null, l.subtreeFlags = 0, l.memoizedProps = null, l.memoizedState = null, l.updateQueue = null, l.dependencies = null, l.stateNode = null) : (l.childLanes = a.childLanes, l.lanes = a.lanes, l.child = a.child, l.subtreeFlags = 0, l.deletions = null, l.memoizedProps = a.memoizedProps, l.memoizedState = a.memoizedState, l.updateQueue = a.updateQueue, l.type = a.type, t = a.dependencies, l.dependencies = t === null ? null : {
      lanes: t.lanes,
      firstContext: t.firstContext
    }), l;
  }
  function Iu(l, t, a, e, u, n) {
    var i = 0;
    if (e = l, typeof l == "function") xi(l) && (i = 1);
    else if (typeof l == "string")
      i = Gv(
        l,
        a,
        O.current
      ) ? 26 : l === "html" || l === "head" || l === "body" ? 27 : 5;
    else
      l: switch (l) {
        case kl:
          return l = dt(31, a, t, u), l.elementType = kl, l.lanes = n, l;
        case Cl:
          return Ga(a.children, u, n, t);
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
              case Zl:
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
  function Ga(l, t, a, e) {
    return l = dt(7, l, e, t), l.lanes = a, l;
  }
  function ji(l, t, a) {
    return l = dt(6, l, null, t), l.lanes = a, l;
  }
  function bs(l) {
    var t = dt(18, null, null, 0);
    return t.stateNode = l, t;
  }
  function _i(l, t, a) {
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
  var ps = /* @__PURE__ */ new WeakMap();
  function Tt(l, t) {
    if (typeof l == "object" && l !== null) {
      var a = ps.get(l);
      return a !== void 0 ? a : (t = {
        value: l,
        source: t,
        stack: M(t)
      }, ps.set(l, t), t);
    }
    return {
      value: l,
      source: t,
      stack: M(t)
    };
  }
  var me = [], he = 0, Pu = null, Ie = 0, At = [], xt = 0, sa = null, Yt = 1, Bt = "";
  function Jt(l, t) {
    me[he++] = Ie, me[he++] = Pu, Pu = l, Ie = t;
  }
  function zs(l, t, a) {
    At[xt++] = Yt, At[xt++] = Bt, At[xt++] = sa, sa = l;
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
  function Mi(l) {
    l.return !== null && (Jt(l, 1), zs(l, 1, 0));
  }
  function Ni(l) {
    for (; l === Pu; )
      Pu = me[--he], me[he] = null, Ie = me[--he], me[he] = null;
    for (; l === sa; )
      sa = At[--xt], At[xt] = null, Bt = At[--xt], At[xt] = null, Yt = At[--xt], At[xt] = null;
  }
  function Es(l, t) {
    At[xt++] = Yt, At[xt++] = Bt, At[xt++] = sa, Yt = t.id, Bt = t.overflow, sa = l;
  }
  var Kl = null, yl = null, I = !1, da = null, jt = !1, Oi = Error(g(519));
  function oa(l) {
    var t = Error(
      g(
        418,
        1 < arguments.length && arguments[1] !== void 0 && arguments[1] ? "text" : "HTML",
        ""
      )
    );
    throw Pe(Tt(t, l)), Oi;
  }
  function Ts(l) {
    var t = l.stateNode, a = l.type, e = l.memoizedProps;
    switch (t[Vl] = l, t[lt] = e, a) {
      case "dialog":
        $("cancel", t), $("close", t);
        break;
      case "iframe":
      case "object":
      case "embed":
        $("load", t);
        break;
      case "video":
      case "audio":
        for (a = 0; a < zu.length; a++)
          $(zu[a], t);
        break;
      case "source":
        $("error", t);
        break;
      case "img":
      case "image":
      case "link":
        $("error", t), $("load", t);
        break;
      case "details":
        $("toggle", t);
        break;
      case "input":
        $("invalid", t), qf(
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
        $("invalid", t);
        break;
      case "textarea":
        $("invalid", t), Bf(t, e.value, e.defaultValue, e.children);
    }
    a = e.children, typeof a != "string" && typeof a != "number" && typeof a != "bigint" || t.textContent === "" + a || e.suppressHydrationWarning === !0 || Qo(t.textContent, a) ? (e.popover != null && ($("beforetoggle", t), $("toggle", t)), e.onScroll != null && $("scroll", t), e.onScrollEnd != null && $("scrollend", t), e.onClick != null && (t.onclick = Lt), t = !0) : t = !1, t || oa(l, !0);
  }
  function As(l) {
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
  function ve(l) {
    if (l !== Kl) return !1;
    if (!I) return As(l), I = !0, !1;
    var t = l.tag, a;
    if ((a = t !== 3 && t !== 27) && ((a = t === 5) && (a = l.type, a = !(a !== "form" && a !== "button") || Wc(l.type, l.memoizedProps)), a = !a), a && yl && oa(l), As(l), t === 13) {
      if (l = l.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(317));
      yl = ko(l);
    } else if (t === 31) {
      if (l = l.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(317));
      yl = ko(l);
    } else
      t === 27 ? (t = yl, xa(l.type) ? (l = Pc, Pc = null, yl = l) : yl = t) : yl = Kl ? Mt(l.stateNode.nextSibling) : null;
    return !0;
  }
  function Xa() {
    yl = Kl = null, I = !1;
  }
  function Di() {
    var l = da;
    return l !== null && (nt === null ? nt = l : nt.push.apply(
      nt,
      l
    ), da = null), l;
  }
  function Pe(l) {
    da === null ? da = [l] : da.push(l);
  }
  var Ui = o(null), Qa = null, wt = null;
  function ma(l, t, a) {
    j(Ui, t._currentValue), t._currentValue = a;
  }
  function Wt(l) {
    l._currentValue = Ui.current, E(Ui);
  }
  function Ri(l, t, a) {
    for (; l !== null; ) {
      var e = l.alternate;
      if ((l.childLanes & t) !== t ? (l.childLanes |= t, e !== null && (e.childLanes |= t)) : e !== null && (e.childLanes & t) !== t && (e.childLanes |= t), l === a) break;
      l = l.return;
    }
  }
  function Ci(l, t, a, e) {
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
  function ye(l, t, a, e) {
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
      } else if (u === ll.current) {
        if (i = u.alternate, i === null) throw Error(g(387));
        i.memoizedState.memoizedState !== u.memoizedState.memoizedState && (l !== null ? l.push(ju) : l = [ju]);
      }
      u = u.return;
    }
    l !== null && Ci(
      t,
      l,
      a,
      e
    ), t.flags |= 262144;
  }
  function ln(l) {
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
  function Za(l) {
    Qa = l, wt = null, l = l.dependencies, l !== null && (l.firstContext = null);
  }
  function Jl(l) {
    return xs(Qa, l);
  }
  function tn(l, t) {
    return Qa === null && Za(l), xs(l, t);
  }
  function xs(l, t) {
    var a = t._currentValue;
    if (t = { context: t, memoizedValue: a, next: null }, wt === null) {
      if (l === null) throw Error(g(308));
      wt = t, l.dependencies = { lanes: 0, firstContext: t }, l.flags |= 524288;
    } else wt = wt.next = t;
    return a;
  }
  var Hh = typeof AbortController < "u" ? AbortController : function() {
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
  }, qh = N.unstable_scheduleCallback, Yh = N.unstable_NormalPriority, Ml = {
    $$typeof: Bl,
    Consumer: null,
    Provider: null,
    _currentValue: null,
    _currentValue2: null,
    _threadCount: 0
  };
  function Hi() {
    return {
      controller: new Hh(),
      data: /* @__PURE__ */ new Map(),
      refCount: 0
    };
  }
  function lu(l) {
    l.refCount--, l.refCount === 0 && qh(Yh, function() {
      l.controller.abort();
    });
  }
  var tu = null, qi = 0, re = 0, ge = null;
  function Bh(l, t) {
    if (tu === null) {
      var a = tu = [];
      qi = 0, re = Gc(), ge = {
        status: "pending",
        value: void 0,
        then: function(e) {
          a.push(e);
        }
      };
    }
    return qi++, t.then(js, js), t;
  }
  function js() {
    if (--qi === 0 && tu !== null) {
      ge !== null && (ge.status = "fulfilled");
      var l = tu;
      tu = null, re = 0, ge = null;
      for (var t = 0; t < l.length; t++) (0, l[t])();
    }
  }
  function Gh(l, t) {
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
  var _s = b.S;
  b.S = function(l, t) {
    oo = Fl(), typeof t == "object" && t !== null && typeof t.then == "function" && Bh(l, t), _s !== null && _s(l, t);
  };
  var La = o(null);
  function Yi() {
    var l = La.current;
    return l !== null ? l : ml.pooledCache;
  }
  function an(l, t) {
    t === null ? j(La, La.current) : j(La, t.pool);
  }
  function Ms() {
    var l = Yi();
    return l === null ? null : { parent: Ml._currentValue, pool: l };
  }
  var Se = Error(g(460)), Bi = Error(g(474)), en = Error(g(542)), un = { then: function() {
  } };
  function Ns(l) {
    return l = l.status, l === "fulfilled" || l === "rejected";
  }
  function Os(l, t, a) {
    switch (a = l[a], a === void 0 ? l.push(t) : a !== t && (t.then(Lt, Lt), t = a), t.status) {
      case "fulfilled":
        return t.value;
      case "rejected":
        throw l = t.reason, Us(l), l;
      default:
        if (typeof t.status == "string") t.then(Lt, Lt);
        else {
          if (l = ml, l !== null && 100 < l.shellSuspendCounter)
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
            throw l = t.reason, Us(l), l;
        }
        throw Ka = t, Se;
    }
  }
  function Va(l) {
    try {
      var t = l._init;
      return t(l._payload);
    } catch (a) {
      throw a !== null && typeof a == "object" && typeof a.then == "function" ? (Ka = a, Se) : a;
    }
  }
  var Ka = null;
  function Ds() {
    if (Ka === null) throw Error(g(459));
    var l = Ka;
    return Ka = null, l;
  }
  function Us(l) {
    if (l === Se || l === en)
      throw Error(g(483));
  }
  var be = null, au = 0;
  function nn(l) {
    var t = au;
    return au += 1, be === null && (be = []), Os(be, l, t);
  }
  function eu(l, t) {
    t = t.props.ref, l.ref = t !== void 0 ? t : null;
  }
  function cn(l, t) {
    throw t.$$typeof === vl ? Error(g(525)) : (l = Object.prototype.toString.call(t), Error(
      g(
        31,
        l === "[object Object]" ? "object with keys {" + Object.keys(t).join(", ") + "}" : l
      )
    ));
  }
  function Rs(l) {
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
    function c(h, m, v, T) {
      return m === null || m.tag !== 6 ? (m = ji(v, h.mode, T), m.return = h, m) : (m = u(m, v), m.return = h, m);
    }
    function s(h, m, v, T) {
      var H = v.type;
      return H === Cl ? z(
        h,
        m,
        v.props.children,
        T,
        v.key
      ) : m !== null && (m.elementType === H || typeof H == "object" && H !== null && H.$$typeof === Ll && Va(H) === m.type) ? (m = u(m, v.props), eu(m, v), m.return = h, m) : (m = Iu(
        v.type,
        v.key,
        v.props,
        null,
        h.mode,
        T
      ), eu(m, v), m.return = h, m);
    }
    function y(h, m, v, T) {
      return m === null || m.tag !== 4 || m.stateNode.containerInfo !== v.containerInfo || m.stateNode.implementation !== v.implementation ? (m = _i(v, h.mode, T), m.return = h, m) : (m = u(m, v.children || []), m.return = h, m);
    }
    function z(h, m, v, T, H) {
      return m === null || m.tag !== 7 ? (m = Ga(
        v,
        h.mode,
        T,
        H
      ), m.return = h, m) : (m = u(m, v), m.return = h, m);
    }
    function A(h, m, v) {
      if (typeof m == "string" && m !== "" || typeof m == "number" || typeof m == "bigint")
        return m = ji(
          "" + m,
          h.mode,
          v
        ), m.return = h, m;
      if (typeof m == "object" && m !== null) {
        switch (m.$$typeof) {
          case Al:
            return v = Iu(
              m.type,
              m.key,
              m.props,
              null,
              h.mode,
              v
            ), eu(v, m), v.return = h, v;
          case Ql:
            return m = _i(
              m,
              h.mode,
              v
            ), m.return = h, m;
          case Ll:
            return m = Va(m), A(h, m, v);
        }
        if (Pl(m) || zl(m))
          return m = Ga(
            m,
            h.mode,
            v,
            null
          ), m.return = h, m;
        if (typeof m.then == "function")
          return A(h, nn(m), v);
        if (m.$$typeof === Bl)
          return A(
            h,
            tn(h, m),
            v
          );
        cn(h, m);
      }
      return null;
    }
    function r(h, m, v, T) {
      var H = m !== null ? m.key : null;
      if (typeof v == "string" && v !== "" || typeof v == "number" || typeof v == "bigint")
        return H !== null ? null : c(h, m, "" + v, T);
      if (typeof v == "object" && v !== null) {
        switch (v.$$typeof) {
          case Al:
            return v.key === H ? s(h, m, v, T) : null;
          case Ql:
            return v.key === H ? y(h, m, v, T) : null;
          case Ll:
            return v = Va(v), r(h, m, v, T);
        }
        if (Pl(v) || zl(v))
          return H !== null ? null : z(h, m, v, T, null);
        if (typeof v.then == "function")
          return r(
            h,
            m,
            nn(v),
            T
          );
        if (v.$$typeof === Bl)
          return r(
            h,
            m,
            tn(h, v),
            T
          );
        cn(h, v);
      }
      return null;
    }
    function S(h, m, v, T, H) {
      if (typeof T == "string" && T !== "" || typeof T == "number" || typeof T == "bigint")
        return h = h.get(v) || null, c(m, h, "" + T, H);
      if (typeof T == "object" && T !== null) {
        switch (T.$$typeof) {
          case Al:
            return h = h.get(
              T.key === null ? v : T.key
            ) || null, s(m, h, T, H);
          case Ql:
            return h = h.get(
              T.key === null ? v : T.key
            ) || null, y(m, h, T, H);
          case Ll:
            return T = Va(T), S(
              h,
              m,
              v,
              T,
              H
            );
        }
        if (Pl(T) || zl(T))
          return h = h.get(v) || null, z(m, h, T, H, null);
        if (typeof T.then == "function")
          return S(
            h,
            m,
            v,
            nn(T),
            H
          );
        if (T.$$typeof === Bl)
          return S(
            h,
            m,
            v,
            tn(m, T),
            H
          );
        cn(m, T);
      }
      return null;
    }
    function D(h, m, v, T) {
      for (var H = null, tl = null, U = m, V = m = 0, F = null; U !== null && V < v.length; V++) {
        U.index > V ? (F = U, U = null) : F = U.sibling;
        var al = r(
          h,
          U,
          v[V],
          T
        );
        if (al === null) {
          U === null && (U = F);
          break;
        }
        l && U && al.alternate === null && t(h, U), m = n(al, m, V), tl === null ? H = al : tl.sibling = al, tl = al, U = F;
      }
      if (V === v.length)
        return a(h, U), I && Jt(h, V), H;
      if (U === null) {
        for (; V < v.length; V++)
          U = A(h, v[V], T), U !== null && (m = n(
            U,
            m,
            V
          ), tl === null ? H = U : tl.sibling = U, tl = U);
        return I && Jt(h, V), H;
      }
      for (U = e(U); V < v.length; V++)
        F = S(
          U,
          h,
          V,
          v[V],
          T
        ), F !== null && (l && F.alternate !== null && U.delete(
          F.key === null ? V : F.key
        ), m = n(
          F,
          m,
          V
        ), tl === null ? H = F : tl.sibling = F, tl = F);
      return l && U.forEach(function(Oa) {
        return t(h, Oa);
      }), I && Jt(h, V), H;
    }
    function B(h, m, v, T) {
      if (v == null) throw Error(g(151));
      for (var H = null, tl = null, U = m, V = m = 0, F = null, al = v.next(); U !== null && !al.done; V++, al = v.next()) {
        U.index > V ? (F = U, U = null) : F = U.sibling;
        var Oa = r(h, U, al.value, T);
        if (Oa === null) {
          U === null && (U = F);
          break;
        }
        l && U && Oa.alternate === null && t(h, U), m = n(Oa, m, V), tl === null ? H = Oa : tl.sibling = Oa, tl = Oa, U = F;
      }
      if (al.done)
        return a(h, U), I && Jt(h, V), H;
      if (U === null) {
        for (; !al.done; V++, al = v.next())
          al = A(h, al.value, T), al !== null && (m = n(al, m, V), tl === null ? H = al : tl.sibling = al, tl = al);
        return I && Jt(h, V), H;
      }
      for (U = e(U); !al.done; V++, al = v.next())
        al = S(U, h, V, al.value, T), al !== null && (l && al.alternate !== null && U.delete(al.key === null ? V : al.key), m = n(al, m, V), tl === null ? H = al : tl.sibling = al, tl = al);
      return l && U.forEach(function(kv) {
        return t(h, kv);
      }), I && Jt(h, V), H;
    }
    function ol(h, m, v, T) {
      if (typeof v == "object" && v !== null && v.type === Cl && v.key === null && (v = v.props.children), typeof v == "object" && v !== null) {
        switch (v.$$typeof) {
          case Al:
            l: {
              for (var H = v.key; m !== null; ) {
                if (m.key === H) {
                  if (H = v.type, H === Cl) {
                    if (m.tag === 7) {
                      a(
                        h,
                        m.sibling
                      ), T = u(
                        m,
                        v.props.children
                      ), T.return = h, h = T;
                      break l;
                    }
                  } else if (m.elementType === H || typeof H == "object" && H !== null && H.$$typeof === Ll && Va(H) === m.type) {
                    a(
                      h,
                      m.sibling
                    ), T = u(m, v.props), eu(T, v), T.return = h, h = T;
                    break l;
                  }
                  a(h, m);
                  break;
                } else t(h, m);
                m = m.sibling;
              }
              v.type === Cl ? (T = Ga(
                v.props.children,
                h.mode,
                T,
                v.key
              ), T.return = h, h = T) : (T = Iu(
                v.type,
                v.key,
                v.props,
                null,
                h.mode,
                T
              ), eu(T, v), T.return = h, h = T);
            }
            return i(h);
          case Ql:
            l: {
              for (H = v.key; m !== null; ) {
                if (m.key === H)
                  if (m.tag === 4 && m.stateNode.containerInfo === v.containerInfo && m.stateNode.implementation === v.implementation) {
                    a(
                      h,
                      m.sibling
                    ), T = u(m, v.children || []), T.return = h, h = T;
                    break l;
                  } else {
                    a(h, m);
                    break;
                  }
                else t(h, m);
                m = m.sibling;
              }
              T = _i(v, h.mode, T), T.return = h, h = T;
            }
            return i(h);
          case Ll:
            return v = Va(v), ol(
              h,
              m,
              v,
              T
            );
        }
        if (Pl(v))
          return D(
            h,
            m,
            v,
            T
          );
        if (zl(v)) {
          if (H = zl(v), typeof H != "function") throw Error(g(150));
          return v = H.call(v), B(
            h,
            m,
            v,
            T
          );
        }
        if (typeof v.then == "function")
          return ol(
            h,
            m,
            nn(v),
            T
          );
        if (v.$$typeof === Bl)
          return ol(
            h,
            m,
            tn(h, v),
            T
          );
        cn(h, v);
      }
      return typeof v == "string" && v !== "" || typeof v == "number" || typeof v == "bigint" ? (v = "" + v, m !== null && m.tag === 6 ? (a(h, m.sibling), T = u(m, v), T.return = h, h = T) : (a(h, m), T = ji(v, h.mode, T), T.return = h, h = T), i(h)) : a(h, m);
    }
    return function(h, m, v, T) {
      try {
        au = 0;
        var H = ol(
          h,
          m,
          v,
          T
        );
        return be = null, H;
      } catch (U) {
        if (U === Se || U === en) throw U;
        var tl = dt(29, U, null, h.mode);
        return tl.lanes = T, tl.return = h, tl;
      }
    };
  }
  var Ja = Rs(!0), Cs = Rs(!1), ha = !1;
  function Gi(l) {
    l.updateQueue = {
      baseState: l.memoizedState,
      firstBaseUpdate: null,
      lastBaseUpdate: null,
      shared: { pending: null, lanes: 0, hiddenCallbacks: null },
      callbacks: null
    };
  }
  function Xi(l, t) {
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
    if (e = e.shared, (ul & 2) !== 0) {
      var u = e.pending;
      return u === null ? t.next = t : (t.next = u.next, u.next = t), e.pending = t, t = Fu(l), gs(l, null, a), t;
    }
    return ku(l, e, t, a), Fu(l);
  }
  function uu(l, t, a) {
    if (t = t.updateQueue, t !== null && (t = t.shared, (a & 4194048) !== 0)) {
      var e = t.lanes;
      e &= l.pendingLanes, a |= e, t.lanes = a, xf(l, a);
    }
  }
  function Qi(l, t) {
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
  var Zi = !1;
  function nu() {
    if (Zi) {
      var l = ge;
      if (l !== null) throw l;
    }
  }
  function iu(l, t, a, e) {
    Zi = !1;
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
      var A = u.baseState;
      i = 0, z = y = s = null, c = n;
      do {
        var r = c.lane & -536870913, S = r !== c.lane;
        if (S ? (k & r) === r : (e & r) === r) {
          r !== 0 && r === re && (Zi = !0), z !== null && (z = z.next = {
            lane: 0,
            tag: c.tag,
            payload: c.payload,
            callback: null,
            next: null
          });
          l: {
            var D = l, B = c;
            r = t;
            var ol = a;
            switch (B.tag) {
              case 1:
                if (D = B.payload, typeof D == "function") {
                  A = D.call(ol, A, r);
                  break l;
                }
                A = D;
                break l;
              case 3:
                D.flags = D.flags & -65537 | 128;
              case 0:
                if (D = B.payload, r = typeof D == "function" ? D.call(ol, A, r) : D, r == null) break l;
                A = R({}, A, r);
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
          }, z === null ? (y = z = S, s = A) : z = z.next = S, i |= r;
        if (c = c.next, c === null) {
          if (c = u.shared.pending, c === null)
            break;
          S = c, c = S.next, S.next = null, u.lastBaseUpdate = S, u.shared.pending = null;
        }
      } while (!0);
      z === null && (s = A), u.baseState = s, u.firstBaseUpdate = y, u.lastBaseUpdate = z, n === null && (u.shared.lanes = 0), pa |= i, l.lanes = i, l.memoizedState = A;
    }
  }
  function Hs(l, t) {
    if (typeof l != "function")
      throw Error(g(191, l));
    l.call(t);
  }
  function qs(l, t) {
    var a = l.callbacks;
    if (a !== null)
      for (l.callbacks = null, l = 0; l < a.length; l++)
        Hs(a[l], t);
  }
  var pe = o(null), fn = o(0);
  function Ys(l, t) {
    l = ea, j(fn, l), j(pe, t), ea = l | t.baseLanes;
  }
  function Li() {
    j(fn, ea), j(pe, pe.current);
  }
  function Vi() {
    ea = fn.current, E(pe), E(fn);
  }
  var ot = o(null), _t = null;
  function ra(l) {
    var t = l.alternate;
    j(xl, xl.current & 1), j(ot, l), _t === null && (t === null || pe.current !== null || t.memoizedState !== null) && (_t = l);
  }
  function Ki(l) {
    j(xl, xl.current), j(ot, l), _t === null && (_t = l);
  }
  function Bs(l) {
    l.tag === 22 ? (j(xl, xl.current), j(ot, l), _t === null && (_t = l)) : ga();
  }
  function ga() {
    j(xl, xl.current), j(ot, ot.current);
  }
  function mt(l) {
    E(ot), _t === l && (_t = null), E(xl);
  }
  var xl = o(0);
  function sn(l) {
    for (var t = l; t !== null; ) {
      if (t.tag === 13) {
        var a = t.memoizedState;
        if (a !== null && (a = a.dehydrated, a === null || Fc(a) || Ic(a)))
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
  var $t = 0, L = null, sl = null, Nl = null, dn = !1, ze = !1, wa = !1, on = 0, cu = 0, Ee = null, Xh = 0;
  function El() {
    throw Error(g(321));
  }
  function Ji(l, t) {
    if (t === null) return !1;
    for (var a = 0; a < t.length && a < l.length; a++)
      if (!st(l[a], t[a])) return !1;
    return !0;
  }
  function wi(l, t, a, e, u, n) {
    return $t = n, L = t, t.memoizedState = null, t.updateQueue = null, t.lanes = 0, b.H = l === null || l.memoizedState === null ? zd : fc, wa = !1, n = a(e, u), wa = !1, ze && (n = Xs(
      t,
      a,
      e,
      u
    )), Gs(l), n;
  }
  function Gs(l) {
    b.H = du;
    var t = sl !== null && sl.next !== null;
    if ($t = 0, Nl = sl = L = null, dn = !1, cu = 0, Ee = null, t) throw Error(g(300));
    l === null || Ol || (l = l.dependencies, l !== null && ln(l) && (Ol = !0));
  }
  function Xs(l, t, a, e) {
    L = l;
    var u = 0;
    do {
      if (ze && (Ee = null), cu = 0, ze = !1, 25 <= u) throw Error(g(301));
      if (u += 1, Nl = sl = null, l.updateQueue != null) {
        var n = l.updateQueue;
        n.lastEffect = null, n.events = null, n.stores = null, n.memoCache != null && (n.memoCache.index = 0);
      }
      b.H = Ed, n = t(a, e);
    } while (ze);
    return n;
  }
  function Qh() {
    var l = b.H, t = l.useState()[0];
    return t = typeof t.then == "function" ? fu(t) : t, l = l.useState()[0], (sl !== null ? sl.memoizedState : null) !== l && (L.flags |= 1024), t;
  }
  function Wi() {
    var l = on !== 0;
    return on = 0, l;
  }
  function $i(l, t, a) {
    t.updateQueue = l.updateQueue, t.flags &= -2053, l.lanes &= ~a;
  }
  function ki(l) {
    if (dn) {
      for (l = l.memoizedState; l !== null; ) {
        var t = l.queue;
        t !== null && (t.pending = null), l = l.next;
      }
      dn = !1;
    }
    $t = 0, Nl = sl = L = null, ze = !1, cu = on = 0, Ee = null;
  }
  function Il() {
    var l = {
      memoizedState: null,
      baseState: null,
      baseQueue: null,
      queue: null,
      next: null
    };
    return Nl === null ? L.memoizedState = Nl = l : Nl = Nl.next = l, Nl;
  }
  function jl() {
    if (sl === null) {
      var l = L.alternate;
      l = l !== null ? l.memoizedState : null;
    } else l = sl.next;
    var t = Nl === null ? L.memoizedState : Nl.next;
    if (t !== null)
      Nl = t, sl = l;
    else {
      if (l === null)
        throw L.alternate === null ? Error(g(467)) : Error(g(310));
      sl = l, l = {
        memoizedState: sl.memoizedState,
        baseState: sl.baseState,
        baseQueue: sl.baseQueue,
        queue: sl.queue,
        next: null
      }, Nl === null ? L.memoizedState = Nl = l : Nl = Nl.next = l;
    }
    return Nl;
  }
  function mn() {
    return { lastEffect: null, events: null, stores: null, memoCache: null };
  }
  function fu(l) {
    var t = cu;
    return cu += 1, Ee === null && (Ee = []), l = Os(Ee, l, t), t = L, (Nl === null ? t.memoizedState : Nl.next) === null && (t = t.alternate, b.H = t === null || t.memoizedState === null ? zd : fc), l;
  }
  function hn(l) {
    if (l !== null && typeof l == "object") {
      if (typeof l.then == "function") return fu(l);
      if (l.$$typeof === Bl) return Jl(l);
    }
    throw Error(g(438, String(l)));
  }
  function Fi(l) {
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
    if (t == null && (t = { data: [], index: 0 }), a === null && (a = mn(), L.updateQueue = a), a.memoCache = t, a = t.data[t.index], a === void 0)
      for (a = t.data[t.index] = Array(l), e = 0; e < l; e++)
        a[e] = Qt;
    return t.index++, a;
  }
  function kt(l, t) {
    return typeof t == "function" ? t(l) : t;
  }
  function vn(l) {
    var t = jl();
    return Ii(t, sl, l);
  }
  function Ii(l, t, a) {
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
        var A = y.lane & -536870913;
        if (A !== y.lane ? (k & A) === A : ($t & A) === A) {
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
            }), A === re && (z = !0);
          else if (($t & r) === r) {
            y = y.next, r === re && (z = !0);
            continue;
          } else
            A = {
              lane: 0,
              revertLane: y.revertLane,
              gesture: null,
              action: y.action,
              hasEagerState: y.hasEagerState,
              eagerState: y.eagerState,
              next: null
            }, s === null ? (c = s = A, i = n) : s = s.next = A, L.lanes |= r, pa |= r;
          A = y.action, wa && a(n, A), n = y.hasEagerState ? y.eagerState : a(n, A);
        } else
          r = {
            lane: A,
            revertLane: y.revertLane,
            gesture: y.gesture,
            action: y.action,
            hasEagerState: y.hasEagerState,
            eagerState: y.eagerState,
            next: null
          }, s === null ? (c = s = r, i = n) : s = s.next = r, L.lanes |= A, pa |= A;
        y = y.next;
      } while (y !== null && y !== t);
      if (s === null ? i = n : s.next = c, !st(n, l.memoizedState) && (Ol = !0, z && (a = ge, a !== null)))
        throw a;
      l.memoizedState = n, l.baseState = i, l.baseQueue = s, e.lastRenderedState = n;
    }
    return u === null && (e.lanes = 0), [l.memoizedState, e.dispatch];
  }
  function Pi(l) {
    var t = jl(), a = t.queue;
    if (a === null) throw Error(g(311));
    a.lastRenderedReducer = l;
    var e = a.dispatch, u = a.pending, n = t.memoizedState;
    if (u !== null) {
      a.pending = null;
      var i = u = u.next;
      do
        n = l(n, i.action), i = i.next;
      while (i !== u);
      st(n, t.memoizedState) || (Ol = !0), t.memoizedState = n, t.baseQueue === null && (t.baseState = n), a.lastRenderedState = n;
    }
    return [n, e];
  }
  function Qs(l, t, a) {
    var e = L, u = jl(), n = I;
    if (n) {
      if (a === void 0) throw Error(g(407));
      a = a();
    } else a = t();
    var i = !st(
      (sl || u).memoizedState,
      a
    );
    if (i && (u.memoizedState = a, Ol = !0), u = u.queue, ac(Vs.bind(null, e, u, l), [
      l
    ]), u.getSnapshot !== t || i || Nl !== null && Nl.memoizedState.tag & 1) {
      if (e.flags |= 2048, Te(
        9,
        { destroy: void 0 },
        Ls.bind(
          null,
          e,
          u,
          a,
          t
        ),
        null
      ), ml === null) throw Error(g(349));
      n || ($t & 127) !== 0 || Zs(e, t, a);
    }
    return a;
  }
  function Zs(l, t, a) {
    l.flags |= 16384, l = { getSnapshot: t, value: a }, t = L.updateQueue, t === null ? (t = mn(), L.updateQueue = t, t.stores = [l]) : (a = t.stores, a === null ? t.stores = [l] : a.push(l));
  }
  function Ls(l, t, a, e) {
    t.value = a, t.getSnapshot = e, Ks(t) && Js(l);
  }
  function Vs(l, t, a) {
    return a(function() {
      Ks(t) && Js(l);
    });
  }
  function Ks(l) {
    var t = l.getSnapshot;
    l = l.value;
    try {
      var a = t();
      return !st(l, a);
    } catch {
      return !0;
    }
  }
  function Js(l) {
    var t = Ba(l, 2);
    t !== null && it(t, l, 2);
  }
  function lc(l) {
    var t = Il();
    if (typeof l == "function") {
      var a = l;
      if (l = a(), wa) {
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
  function ws(l, t, a, e) {
    return l.baseState = a, Ii(
      l,
      sl,
      typeof e == "function" ? e : kt
    );
  }
  function Zh(l, t, a, e, u) {
    if (gn(l)) throw Error(g(485));
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
      b.T !== null ? a(!0) : n.isTransition = !1, e(n), a = t.pending, a === null ? (n.next = t.pending = n, Ws(t, n)) : (n.next = a.next, t.pending = a.next = n);
    }
  }
  function Ws(l, t) {
    var a = t.action, e = t.payload, u = l.state;
    if (t.isTransition) {
      var n = b.T, i = {};
      b.T = i;
      try {
        var c = a(u, e), s = b.S;
        s !== null && s(i, c), $s(l, t, c);
      } catch (y) {
        tc(l, t, y);
      } finally {
        n !== null && i.types !== null && (n.types = i.types), b.T = n;
      }
    } else
      try {
        n = a(u, e), $s(l, t, n);
      } catch (y) {
        tc(l, t, y);
      }
  }
  function $s(l, t, a) {
    a !== null && typeof a == "object" && typeof a.then == "function" ? a.then(
      function(e) {
        ks(l, t, e);
      },
      function(e) {
        return tc(l, t, e);
      }
    ) : ks(l, t, a);
  }
  function ks(l, t, a) {
    t.status = "fulfilled", t.value = a, Fs(t), l.state = a, t = l.pending, t !== null && (a = t.next, a === t ? l.pending = null : (a = a.next, t.next = a, Ws(l, a)));
  }
  function tc(l, t, a) {
    var e = l.pending;
    if (l.pending = null, e !== null) {
      e = e.next;
      do
        t.status = "rejected", t.reason = a, Fs(t), t = t.next;
      while (t !== e);
    }
    l.action = null;
  }
  function Fs(l) {
    l = l.listeners;
    for (var t = 0; t < l.length; t++) (0, l[t])();
  }
  function Is(l, t) {
    return t;
  }
  function Ps(l, t) {
    if (I) {
      var a = ml.formState;
      if (a !== null) {
        l: {
          var e = L;
          if (I) {
            if (yl) {
              t: {
                for (var u = yl, n = jt; u.nodeType !== 8; ) {
                  if (!n) {
                    u = null;
                    break t;
                  }
                  if (u = Mt(
                    u.nextSibling
                  ), u === null) {
                    u = null;
                    break t;
                  }
                }
                n = u.data, u = n === "F!" || n === "F" ? u : null;
              }
              if (u) {
                yl = Mt(
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
      lastRenderedReducer: Is,
      lastRenderedState: t
    }, a.queue = e, a = Sd.bind(
      null,
      L,
      e
    ), e.dispatch = a, e = lc(!1), n = cc.bind(
      null,
      L,
      !1,
      e.queue
    ), e = Il(), u = {
      state: t,
      dispatch: null,
      action: l,
      pending: null
    }, e.queue = u, a = Zh.bind(
      null,
      L,
      u,
      n,
      a
    ), u.dispatch = a, e.memoizedState = l, [t, a, !1];
  }
  function ld(l) {
    var t = jl();
    return td(t, sl, l);
  }
  function td(l, t, a) {
    if (t = Ii(
      l,
      t,
      Is
    )[0], l = vn(kt)[0], typeof t == "object" && t !== null && typeof t.then == "function")
      try {
        var e = fu(t);
      } catch (i) {
        throw i === Se ? en : i;
      }
    else e = t;
    t = jl();
    var u = t.queue, n = u.dispatch;
    return a !== t.memoizedState && (L.flags |= 2048, Te(
      9,
      { destroy: void 0 },
      Lh.bind(null, u, a),
      null
    )), [e, n, l];
  }
  function Lh(l, t) {
    l.action = t;
  }
  function ad(l) {
    var t = jl(), a = sl;
    if (a !== null)
      return td(t, a, l);
    jl(), t = t.memoizedState, a = jl();
    var e = a.queue.dispatch;
    return a.memoizedState = l, [t, e, !1];
  }
  function Te(l, t, a, e) {
    return l = { tag: l, create: a, deps: e, inst: t, next: null }, t = L.updateQueue, t === null && (t = mn(), L.updateQueue = t), a = t.lastEffect, a === null ? t.lastEffect = l.next = l : (e = a.next, a.next = l, l.next = e, t.lastEffect = l), l;
  }
  function ed() {
    return jl().memoizedState;
  }
  function yn(l, t, a, e) {
    var u = Il();
    L.flags |= l, u.memoizedState = Te(
      1 | t,
      { destroy: void 0 },
      a,
      e === void 0 ? null : e
    );
  }
  function rn(l, t, a, e) {
    var u = jl();
    e = e === void 0 ? null : e;
    var n = u.memoizedState.inst;
    sl !== null && e !== null && Ji(e, sl.memoizedState.deps) ? u.memoizedState = Te(t, n, a, e) : (L.flags |= l, u.memoizedState = Te(
      1 | t,
      n,
      a,
      e
    ));
  }
  function ud(l, t) {
    yn(8390656, 8, l, t);
  }
  function ac(l, t) {
    rn(2048, 8, l, t);
  }
  function Vh(l) {
    L.flags |= 4;
    var t = L.updateQueue;
    if (t === null)
      t = mn(), L.updateQueue = t, t.events = [l];
    else {
      var a = t.events;
      a === null ? t.events = [l] : a.push(l);
    }
  }
  function nd(l) {
    var t = jl().memoizedState;
    return Vh({ ref: t, nextImpl: l }), function() {
      if ((ul & 2) !== 0) throw Error(g(440));
      return t.impl.apply(void 0, arguments);
    };
  }
  function id(l, t) {
    return rn(4, 2, l, t);
  }
  function cd(l, t) {
    return rn(4, 4, l, t);
  }
  function fd(l, t) {
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
  function sd(l, t, a) {
    a = a != null ? a.concat([l]) : null, rn(4, 4, fd.bind(null, t, l), a);
  }
  function ec() {
  }
  function dd(l, t) {
    var a = jl();
    t = t === void 0 ? null : t;
    var e = a.memoizedState;
    return t !== null && Ji(t, e[1]) ? e[0] : (a.memoizedState = [l, t], l);
  }
  function od(l, t) {
    var a = jl();
    t = t === void 0 ? null : t;
    var e = a.memoizedState;
    if (t !== null && Ji(t, e[1]))
      return e[0];
    if (e = l(), wa) {
      ia(!0);
      try {
        l();
      } finally {
        ia(!1);
      }
    }
    return a.memoizedState = [e, t], e;
  }
  function uc(l, t, a) {
    return a === void 0 || ($t & 1073741824) !== 0 && (k & 261930) === 0 ? l.memoizedState = t : (l.memoizedState = a, l = ho(), L.lanes |= l, pa |= l, a);
  }
  function md(l, t, a, e) {
    return st(a, t) ? a : pe.current !== null ? (l = uc(l, a, e), st(l, t) || (Ol = !0), l) : ($t & 42) === 0 || ($t & 1073741824) !== 0 && (k & 261930) === 0 ? (Ol = !0, l.memoizedState = a) : (l = ho(), L.lanes |= l, pa |= l, t);
  }
  function hd(l, t, a, e, u) {
    var n = _.p;
    _.p = n !== 0 && 8 > n ? n : 8;
    var i = b.T, c = {};
    b.T = c, cc(l, !1, t, a);
    try {
      var s = u(), y = b.S;
      if (y !== null && y(c, s), s !== null && typeof s == "object" && typeof s.then == "function") {
        var z = Gh(
          s,
          e
        );
        su(
          l,
          t,
          z,
          yt(l)
        );
      } else
        su(
          l,
          t,
          e,
          yt(l)
        );
    } catch (A) {
      su(
        l,
        t,
        { then: function() {
        }, status: "rejected", reason: A },
        yt()
      );
    } finally {
      _.p = n, i !== null && c.types !== null && (i.types = c.types), b.T = i;
    }
  }
  function Kh() {
  }
  function nc(l, t, a, e) {
    if (l.tag !== 5) throw Error(g(476));
    var u = vd(l).queue;
    hd(
      l,
      u,
      t,
      Y,
      a === null ? Kh : function() {
        return yd(l), a(e);
      }
    );
  }
  function vd(l) {
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
  function yd(l) {
    var t = vd(l);
    t.next === null && (t = l.alternate.memoizedState), su(
      l,
      t.next.queue,
      {},
      yt()
    );
  }
  function ic() {
    return Jl(ju);
  }
  function rd() {
    return jl().memoizedState;
  }
  function gd() {
    return jl().memoizedState;
  }
  function Jh(l) {
    for (var t = l.return; t !== null; ) {
      switch (t.tag) {
        case 24:
        case 3:
          var a = yt();
          l = va(a);
          var e = ya(t, l, a);
          e !== null && (it(e, t, a), uu(e, t, a)), t = { cache: Hi() }, l.payload = t;
          return;
      }
      t = t.return;
    }
  }
  function wh(l, t, a) {
    var e = yt();
    a = {
      lane: e,
      revertLane: 0,
      gesture: null,
      action: a,
      hasEagerState: !1,
      eagerState: null,
      next: null
    }, gn(l) ? bd(t, a) : (a = Ai(l, t, a, e), a !== null && (it(a, l, e), pd(a, t, e)));
  }
  function Sd(l, t, a) {
    var e = yt();
    su(l, t, a, e);
  }
  function su(l, t, a, e) {
    var u = {
      lane: e,
      revertLane: 0,
      gesture: null,
      action: a,
      hasEagerState: !1,
      eagerState: null,
      next: null
    };
    if (gn(l)) bd(t, u);
    else {
      var n = l.alternate;
      if (l.lanes === 0 && (n === null || n.lanes === 0) && (n = t.lastRenderedReducer, n !== null))
        try {
          var i = t.lastRenderedState, c = n(i, a);
          if (u.hasEagerState = !0, u.eagerState = c, st(c, i))
            return ku(l, t, u, 0), ml === null && $u(), !1;
        } catch {
        }
      if (a = Ai(l, t, u, e), a !== null)
        return it(a, l, e), pd(a, t, e), !0;
    }
    return !1;
  }
  function cc(l, t, a, e) {
    if (e = {
      lane: 2,
      revertLane: Gc(),
      gesture: null,
      action: e,
      hasEagerState: !1,
      eagerState: null,
      next: null
    }, gn(l)) {
      if (t) throw Error(g(479));
    } else
      t = Ai(
        l,
        a,
        e,
        2
      ), t !== null && it(t, l, 2);
  }
  function gn(l) {
    var t = l.alternate;
    return l === L || t !== null && t === L;
  }
  function bd(l, t) {
    ze = dn = !0;
    var a = l.pending;
    a === null ? t.next = t : (t.next = a.next, a.next = t), l.pending = t;
  }
  function pd(l, t, a) {
    if ((a & 4194048) !== 0) {
      var e = t.lanes;
      e &= l.pendingLanes, a |= e, t.lanes = a, xf(l, a);
    }
  }
  var du = {
    readContext: Jl,
    use: hn,
    useCallback: El,
    useContext: El,
    useEffect: El,
    useImperativeHandle: El,
    useLayoutEffect: El,
    useInsertionEffect: El,
    useMemo: El,
    useReducer: El,
    useRef: El,
    useState: El,
    useDebugValue: El,
    useDeferredValue: El,
    useTransition: El,
    useSyncExternalStore: El,
    useId: El,
    useHostTransitionStatus: El,
    useFormState: El,
    useActionState: El,
    useOptimistic: El,
    useMemoCache: El,
    useCacheRefresh: El
  };
  du.useEffectEvent = El;
  var zd = {
    readContext: Jl,
    use: hn,
    useCallback: function(l, t) {
      return Il().memoizedState = [
        l,
        t === void 0 ? null : t
      ], l;
    },
    useContext: Jl,
    useEffect: ud,
    useImperativeHandle: function(l, t, a) {
      a = a != null ? a.concat([l]) : null, yn(
        4194308,
        4,
        fd.bind(null, t, l),
        a
      );
    },
    useLayoutEffect: function(l, t) {
      return yn(4194308, 4, l, t);
    },
    useInsertionEffect: function(l, t) {
      yn(4, 2, l, t);
    },
    useMemo: function(l, t) {
      var a = Il();
      t = t === void 0 ? null : t;
      var e = l();
      if (wa) {
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
        if (wa) {
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
      }, e.queue = l, l = l.dispatch = wh.bind(
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
      l = lc(l);
      var t = l.queue, a = Sd.bind(null, L, t);
      return t.dispatch = a, [l.memoizedState, a];
    },
    useDebugValue: ec,
    useDeferredValue: function(l, t) {
      var a = Il();
      return uc(a, l, t);
    },
    useTransition: function() {
      var l = lc(!1);
      return l = hd.bind(
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
        if (a = t(), ml === null)
          throw Error(g(349));
        (k & 127) !== 0 || Zs(e, t, a);
      }
      u.memoizedState = a;
      var n = { value: a, getSnapshot: t };
      return u.queue = n, ud(Vs.bind(null, e, n, l), [
        l
      ]), e.flags |= 2048, Te(
        9,
        { destroy: void 0 },
        Ls.bind(
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
      var l = Il(), t = ml.identifierPrefix;
      if (I) {
        var a = Bt, e = Yt;
        a = (e & ~(1 << 32 - ft(e) - 1)).toString(32) + a, t = "_" + t + "R_" + a, a = on++, 0 < a && (t += "H" + a.toString(32)), t += "_";
      } else
        a = Xh++, t = "_" + t + "r_" + a.toString(32) + "_";
      return l.memoizedState = t;
    },
    useHostTransitionStatus: ic,
    useFormState: Ps,
    useActionState: Ps,
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
      return t.queue = a, t = cc.bind(
        null,
        L,
        !0,
        a
      ), a.dispatch = t, [l, t];
    },
    useMemoCache: Fi,
    useCacheRefresh: function() {
      return Il().memoizedState = Jh.bind(
        null,
        L
      );
    },
    useEffectEvent: function(l) {
      var t = Il(), a = { impl: l };
      return t.memoizedState = a, function() {
        if ((ul & 2) !== 0)
          throw Error(g(440));
        return a.impl.apply(void 0, arguments);
      };
    }
  }, fc = {
    readContext: Jl,
    use: hn,
    useCallback: dd,
    useContext: Jl,
    useEffect: ac,
    useImperativeHandle: sd,
    useInsertionEffect: id,
    useLayoutEffect: cd,
    useMemo: od,
    useReducer: vn,
    useRef: ed,
    useState: function() {
      return vn(kt);
    },
    useDebugValue: ec,
    useDeferredValue: function(l, t) {
      var a = jl();
      return md(
        a,
        sl.memoizedState,
        l,
        t
      );
    },
    useTransition: function() {
      var l = vn(kt)[0], t = jl().memoizedState;
      return [
        typeof l == "boolean" ? l : fu(l),
        t
      ];
    },
    useSyncExternalStore: Qs,
    useId: rd,
    useHostTransitionStatus: ic,
    useFormState: ld,
    useActionState: ld,
    useOptimistic: function(l, t) {
      var a = jl();
      return ws(a, sl, l, t);
    },
    useMemoCache: Fi,
    useCacheRefresh: gd
  };
  fc.useEffectEvent = nd;
  var Ed = {
    readContext: Jl,
    use: hn,
    useCallback: dd,
    useContext: Jl,
    useEffect: ac,
    useImperativeHandle: sd,
    useInsertionEffect: id,
    useLayoutEffect: cd,
    useMemo: od,
    useReducer: Pi,
    useRef: ed,
    useState: function() {
      return Pi(kt);
    },
    useDebugValue: ec,
    useDeferredValue: function(l, t) {
      var a = jl();
      return sl === null ? uc(a, l, t) : md(
        a,
        sl.memoizedState,
        l,
        t
      );
    },
    useTransition: function() {
      var l = Pi(kt)[0], t = jl().memoizedState;
      return [
        typeof l == "boolean" ? l : fu(l),
        t
      ];
    },
    useSyncExternalStore: Qs,
    useId: rd,
    useHostTransitionStatus: ic,
    useFormState: ad,
    useActionState: ad,
    useOptimistic: function(l, t) {
      var a = jl();
      return sl !== null ? ws(a, sl, l, t) : (a.baseState = l, [l, a.queue.dispatch]);
    },
    useMemoCache: Fi,
    useCacheRefresh: gd
  };
  Ed.useEffectEvent = nd;
  function sc(l, t, a, e) {
    t = l.memoizedState, a = a(e, t), a = a == null ? t : R({}, t, a), l.memoizedState = a, l.lanes === 0 && (l.updateQueue.baseState = a);
  }
  var dc = {
    enqueueSetState: function(l, t, a) {
      l = l._reactInternals;
      var e = yt(), u = va(e);
      u.payload = t, a != null && (u.callback = a), t = ya(l, u, e), t !== null && (it(t, l, e), uu(t, l, e));
    },
    enqueueReplaceState: function(l, t, a) {
      l = l._reactInternals;
      var e = yt(), u = va(e);
      u.tag = 1, u.payload = t, a != null && (u.callback = a), t = ya(l, u, e), t !== null && (it(t, l, e), uu(t, l, e));
    },
    enqueueForceUpdate: function(l, t) {
      l = l._reactInternals;
      var a = yt(), e = va(a);
      e.tag = 2, t != null && (e.callback = t), t = ya(l, e, a), t !== null && (it(t, l, a), uu(t, l, a));
    }
  };
  function Td(l, t, a, e, u, n, i) {
    return l = l.stateNode, typeof l.shouldComponentUpdate == "function" ? l.shouldComponentUpdate(e, n, i) : t.prototype && t.prototype.isPureReactComponent ? !ke(a, e) || !ke(u, n) : !0;
  }
  function Ad(l, t, a, e) {
    l = t.state, typeof t.componentWillReceiveProps == "function" && t.componentWillReceiveProps(a, e), typeof t.UNSAFE_componentWillReceiveProps == "function" && t.UNSAFE_componentWillReceiveProps(a, e), t.state !== l && dc.enqueueReplaceState(t, t.state, null);
  }
  function Wa(l, t) {
    var a = t;
    if ("ref" in t) {
      a = {};
      for (var e in t)
        e !== "ref" && (a[e] = t[e]);
    }
    if (l = l.defaultProps) {
      a === t && (a = R({}, a));
      for (var u in l)
        a[u] === void 0 && (a[u] = l[u]);
    }
    return a;
  }
  function xd(l) {
    Wu(l);
  }
  function jd(l) {
    console.error(l);
  }
  function _d(l) {
    Wu(l);
  }
  function Sn(l, t) {
    try {
      var a = l.onUncaughtError;
      a(t.value, { componentStack: t.stack });
    } catch (e) {
      setTimeout(function() {
        throw e;
      });
    }
  }
  function Md(l, t, a) {
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
  function oc(l, t, a) {
    return a = va(a), a.tag = 3, a.payload = { element: null }, a.callback = function() {
      Sn(l, t);
    }, a;
  }
  function Nd(l) {
    return l = va(l), l.tag = 3, l;
  }
  function Od(l, t, a, e) {
    var u = a.type.getDerivedStateFromError;
    if (typeof u == "function") {
      var n = e.value;
      l.payload = function() {
        return u(n);
      }, l.callback = function() {
        Md(t, a, e);
      };
    }
    var i = a.stateNode;
    i !== null && typeof i.componentDidCatch == "function" && (l.callback = function() {
      Md(t, a, e), typeof u != "function" && (za === null ? za = /* @__PURE__ */ new Set([this]) : za.add(this));
      var c = e.stack;
      this.componentDidCatch(e.value, {
        componentStack: c !== null ? c : ""
      });
    });
  }
  function Wh(l, t, a, e, u) {
    if (a.flags |= 32768, e !== null && typeof e == "object" && typeof e.then == "function") {
      if (t = a.alternate, t !== null && ye(
        t,
        a,
        u,
        !0
      ), a = ot.current, a !== null) {
        switch (a.tag) {
          case 31:
          case 13:
            return _t === null ? On() : a.alternate === null && Tl === 0 && (Tl = 3), a.flags &= -257, a.flags |= 65536, a.lanes = u, e === un ? a.flags |= 16384 : (t = a.updateQueue, t === null ? a.updateQueue = /* @__PURE__ */ new Set([e]) : t.add(e), qc(l, e, u)), !1;
          case 22:
            return a.flags |= 65536, e === un ? a.flags |= 16384 : (t = a.updateQueue, t === null ? (t = {
              transitions: null,
              markerInstances: null,
              retryQueue: /* @__PURE__ */ new Set([e])
            }, a.updateQueue = t) : (a = t.retryQueue, a === null ? t.retryQueue = /* @__PURE__ */ new Set([e]) : a.add(e)), qc(l, e, u)), !1;
        }
        throw Error(g(435, a.tag));
      }
      return qc(l, e, u), On(), !1;
    }
    if (I)
      return t = ot.current, t !== null ? ((t.flags & 65536) === 0 && (t.flags |= 256), t.flags |= 65536, t.lanes = u, e !== Oi && (l = Error(g(422), { cause: e }), Pe(Tt(l, a)))) : (e !== Oi && (t = Error(g(423), {
        cause: e
      }), Pe(
        Tt(t, a)
      )), l = l.current.alternate, l.flags |= 65536, u &= -u, l.lanes |= u, e = Tt(e, a), u = oc(
        l.stateNode,
        e,
        u
      ), Qi(l, u), Tl !== 4 && (Tl = 2)), !1;
    var n = Error(g(520), { cause: e });
    if (n = Tt(n, a), Su === null ? Su = [n] : Su.push(n), Tl !== 4 && (Tl = 2), t === null) return !0;
    e = Tt(e, a), a = t;
    do {
      switch (a.tag) {
        case 3:
          return a.flags |= 65536, l = u & -u, a.lanes |= l, l = oc(a.stateNode, e, l), Qi(a, l), !1;
        case 1:
          if (t = a.type, n = a.stateNode, (a.flags & 128) === 0 && (typeof t.getDerivedStateFromError == "function" || n !== null && typeof n.componentDidCatch == "function" && (za === null || !za.has(n))))
            return a.flags |= 65536, u &= -u, a.lanes |= u, u = Nd(u), Od(
              u,
              l,
              a,
              e
            ), Qi(a, u), !1;
      }
      a = a.return;
    } while (a !== null);
    return !1;
  }
  var mc = Error(g(461)), Ol = !1;
  function wl(l, t, a, e) {
    t.child = l === null ? Cs(t, null, a, e) : Ja(
      t,
      l.child,
      a,
      e
    );
  }
  function Dd(l, t, a, e, u) {
    a = a.render;
    var n = t.ref;
    if ("ref" in e) {
      var i = {};
      for (var c in e)
        c !== "ref" && (i[c] = e[c]);
    } else i = e;
    return Za(t), e = wi(
      l,
      t,
      a,
      i,
      n,
      u
    ), c = Wi(), l !== null && !Ol ? ($i(l, t, u), Ft(l, t, u)) : (I && c && Mi(t), t.flags |= 1, wl(l, t, e, u), t.child);
  }
  function Ud(l, t, a, e, u) {
    if (l === null) {
      var n = a.type;
      return typeof n == "function" && !xi(n) && n.defaultProps === void 0 && a.compare === null ? (t.tag = 15, t.type = n, Rd(
        l,
        t,
        n,
        e,
        u
      )) : (l = Iu(
        a.type,
        null,
        e,
        t,
        t.mode,
        u
      ), l.ref = t.ref, l.return = t, t.child = l);
    }
    if (n = l.child, !pc(l, u)) {
      var i = n.memoizedProps;
      if (a = a.compare, a = a !== null ? a : ke, a(i, e) && l.ref === t.ref)
        return Ft(l, t, u);
    }
    return t.flags |= 1, l = Kt(n, e), l.ref = t.ref, l.return = t, t.child = l;
  }
  function Rd(l, t, a, e, u) {
    if (l !== null) {
      var n = l.memoizedProps;
      if (ke(n, e) && l.ref === t.ref)
        if (Ol = !1, t.pendingProps = e = n, pc(l, u))
          (l.flags & 131072) !== 0 && (Ol = !0);
        else
          return t.lanes = l.lanes, Ft(l, t, u);
    }
    return hc(
      l,
      t,
      a,
      e,
      u
    );
  }
  function Cd(l, t, a, e) {
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
        return Hd(
          l,
          t,
          n,
          a,
          e
        );
      }
      if ((a & 536870912) !== 0)
        t.memoizedState = { baseLanes: 0, cachePool: null }, l !== null && an(
          t,
          n !== null ? n.cachePool : null
        ), n !== null ? Ys(t, n) : Li(), Bs(t);
      else
        return e = t.lanes = 536870912, Hd(
          l,
          t,
          n !== null ? n.baseLanes | a : a,
          a,
          e
        );
    } else
      n !== null ? (an(t, n.cachePool), Ys(t, n), ga(), t.memoizedState = null) : (l !== null && an(t, null), Li(), ga());
    return wl(l, t, u, a), t.child;
  }
  function ou(l, t) {
    return l !== null && l.tag === 22 || t.stateNode !== null || (t.stateNode = {
      _visibility: 1,
      _pendingMarkers: null,
      _retryCache: null,
      _transitions: null
    }), t.sibling;
  }
  function Hd(l, t, a, e, u) {
    var n = Yi();
    return n = n === null ? null : { parent: Ml._currentValue, pool: n }, t.memoizedState = {
      baseLanes: a,
      cachePool: n
    }, l !== null && an(t, null), Li(), Bs(t), l !== null && ye(l, t, e, !0), t.childLanes = u, null;
  }
  function bn(l, t) {
    return t = zn(
      { mode: t.mode, children: t.children },
      l.mode
    ), t.ref = l.ref, l.child = t, t.return = l, t;
  }
  function qd(l, t, a) {
    return Ja(t, l.child, null, a), l = bn(t, t.pendingProps), l.flags |= 2, mt(t), t.memoizedState = null, l;
  }
  function $h(l, t, a) {
    var e = t.pendingProps, u = (t.flags & 128) !== 0;
    if (t.flags &= -129, l === null) {
      if (I) {
        if (e.mode === "hidden")
          return l = bn(t, e), t.lanes = 536870912, ou(null, l);
        if (Ki(t), (l = yl) ? (l = $o(
          l,
          jt
        ), l = l !== null && l.data === "&" ? l : null, l !== null && (t.memoizedState = {
          dehydrated: l,
          treeContext: sa !== null ? { id: Yt, overflow: Bt } : null,
          retryLane: 536870912,
          hydrationErrors: null
        }, a = bs(l), a.return = t, t.child = a, Kl = t, yl = null)) : l = null, l === null) throw oa(t);
        return t.lanes = 536870912, null;
      }
      return bn(t, e);
    }
    var n = l.memoizedState;
    if (n !== null) {
      var i = n.dehydrated;
      if (Ki(t), u)
        if (t.flags & 256)
          t.flags &= -257, t = qd(
            l,
            t,
            a
          );
        else if (t.memoizedState !== null)
          t.child = l.child, t.flags |= 128, t = null;
        else throw Error(g(558));
      else if (Ol || ye(l, t, a, !1), u = (a & l.childLanes) !== 0, Ol || u) {
        if (e = ml, e !== null && (i = jf(e, a), i !== 0 && i !== n.retryLane))
          throw n.retryLane = i, Ba(l, i), it(e, l, i), mc;
        On(), t = qd(
          l,
          t,
          a
        );
      } else
        l = n.treeContext, yl = Mt(i.nextSibling), Kl = t, I = !0, da = null, jt = !1, l !== null && Es(t, l), t = bn(t, e), t.flags |= 4096;
      return t;
    }
    return l = Kt(l.child, {
      mode: e.mode,
      children: e.children
    }), l.ref = t.ref, t.child = l, l.return = t, l;
  }
  function pn(l, t) {
    var a = t.ref;
    if (a === null)
      l !== null && l.ref !== null && (t.flags |= 4194816);
    else {
      if (typeof a != "function" && typeof a != "object")
        throw Error(g(284));
      (l === null || l.ref !== a) && (t.flags |= 4194816);
    }
  }
  function hc(l, t, a, e, u) {
    return Za(t), a = wi(
      l,
      t,
      a,
      e,
      void 0,
      u
    ), e = Wi(), l !== null && !Ol ? ($i(l, t, u), Ft(l, t, u)) : (I && e && Mi(t), t.flags |= 1, wl(l, t, a, u), t.child);
  }
  function Yd(l, t, a, e, u, n) {
    return Za(t), t.updateQueue = null, a = Xs(
      t,
      e,
      a,
      u
    ), Gs(l), e = Wi(), l !== null && !Ol ? ($i(l, t, n), Ft(l, t, n)) : (I && e && Mi(t), t.flags |= 1, wl(l, t, a, n), t.child);
  }
  function Bd(l, t, a, e, u) {
    if (Za(t), t.stateNode === null) {
      var n = oe, i = a.contextType;
      typeof i == "object" && i !== null && (n = Jl(i)), n = new a(e, n), t.memoizedState = n.state !== null && n.state !== void 0 ? n.state : null, n.updater = dc, t.stateNode = n, n._reactInternals = t, n = t.stateNode, n.props = e, n.state = t.memoizedState, n.refs = {}, Gi(t), i = a.contextType, n.context = typeof i == "object" && i !== null ? Jl(i) : oe, n.state = t.memoizedState, i = a.getDerivedStateFromProps, typeof i == "function" && (sc(
        t,
        a,
        i,
        e
      ), n.state = t.memoizedState), typeof a.getDerivedStateFromProps == "function" || typeof n.getSnapshotBeforeUpdate == "function" || typeof n.UNSAFE_componentWillMount != "function" && typeof n.componentWillMount != "function" || (i = n.state, typeof n.componentWillMount == "function" && n.componentWillMount(), typeof n.UNSAFE_componentWillMount == "function" && n.UNSAFE_componentWillMount(), i !== n.state && dc.enqueueReplaceState(n, n.state, null), iu(t, e, n, u), nu(), n.state = t.memoizedState), typeof n.componentDidMount == "function" && (t.flags |= 4194308), e = !0;
    } else if (l === null) {
      n = t.stateNode;
      var c = t.memoizedProps, s = Wa(a, c);
      n.props = s;
      var y = n.context, z = a.contextType;
      i = oe, typeof z == "object" && z !== null && (i = Jl(z));
      var A = a.getDerivedStateFromProps;
      z = typeof A == "function" || typeof n.getSnapshotBeforeUpdate == "function", c = t.pendingProps !== c, z || typeof n.UNSAFE_componentWillReceiveProps != "function" && typeof n.componentWillReceiveProps != "function" || (c || y !== i) && Ad(
        t,
        n,
        e,
        i
      ), ha = !1;
      var r = t.memoizedState;
      n.state = r, iu(t, e, n, u), nu(), y = t.memoizedState, c || r !== y || ha ? (typeof A == "function" && (sc(
        t,
        a,
        A,
        e
      ), y = t.memoizedState), (s = ha || Td(
        t,
        a,
        s,
        e,
        r,
        y,
        i
      )) ? (z || typeof n.UNSAFE_componentWillMount != "function" && typeof n.componentWillMount != "function" || (typeof n.componentWillMount == "function" && n.componentWillMount(), typeof n.UNSAFE_componentWillMount == "function" && n.UNSAFE_componentWillMount()), typeof n.componentDidMount == "function" && (t.flags |= 4194308)) : (typeof n.componentDidMount == "function" && (t.flags |= 4194308), t.memoizedProps = e, t.memoizedState = y), n.props = e, n.state = y, n.context = i, e = s) : (typeof n.componentDidMount == "function" && (t.flags |= 4194308), e = !1);
    } else {
      n = t.stateNode, Xi(l, t), i = t.memoizedProps, z = Wa(a, i), n.props = z, A = t.pendingProps, r = n.context, y = a.contextType, s = oe, typeof y == "object" && y !== null && (s = Jl(y)), c = a.getDerivedStateFromProps, (y = typeof c == "function" || typeof n.getSnapshotBeforeUpdate == "function") || typeof n.UNSAFE_componentWillReceiveProps != "function" && typeof n.componentWillReceiveProps != "function" || (i !== A || r !== s) && Ad(
        t,
        n,
        e,
        s
      ), ha = !1, r = t.memoizedState, n.state = r, iu(t, e, n, u), nu();
      var S = t.memoizedState;
      i !== A || r !== S || ha || l !== null && l.dependencies !== null && ln(l.dependencies) ? (typeof c == "function" && (sc(
        t,
        a,
        c,
        e
      ), S = t.memoizedState), (z = ha || Td(
        t,
        a,
        z,
        e,
        r,
        S,
        s
      ) || l !== null && l.dependencies !== null && ln(l.dependencies)) ? (y || typeof n.UNSAFE_componentWillUpdate != "function" && typeof n.componentWillUpdate != "function" || (typeof n.componentWillUpdate == "function" && n.componentWillUpdate(e, S, s), typeof n.UNSAFE_componentWillUpdate == "function" && n.UNSAFE_componentWillUpdate(
        e,
        S,
        s
      )), typeof n.componentDidUpdate == "function" && (t.flags |= 4), typeof n.getSnapshotBeforeUpdate == "function" && (t.flags |= 1024)) : (typeof n.componentDidUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 4), typeof n.getSnapshotBeforeUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 1024), t.memoizedProps = e, t.memoizedState = S), n.props = e, n.state = S, n.context = s, e = z) : (typeof n.componentDidUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 4), typeof n.getSnapshotBeforeUpdate != "function" || i === l.memoizedProps && r === l.memoizedState || (t.flags |= 1024), e = !1);
    }
    return n = e, pn(l, t), e = (t.flags & 128) !== 0, n || e ? (n = t.stateNode, a = e && typeof a.getDerivedStateFromError != "function" ? null : n.render(), t.flags |= 1, l !== null && e ? (t.child = Ja(
      t,
      l.child,
      null,
      u
    ), t.child = Ja(
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
  function Gd(l, t, a, e) {
    return Xa(), t.flags |= 256, wl(l, t, a, e), t.child;
  }
  var vc = {
    dehydrated: null,
    treeContext: null,
    retryLane: 0,
    hydrationErrors: null
  };
  function yc(l) {
    return { baseLanes: l, cachePool: Ms() };
  }
  function rc(l, t, a) {
    return l = l !== null ? l.childLanes & ~a : 0, t && (l |= vt), l;
  }
  function Xd(l, t, a) {
    var e = t.pendingProps, u = !1, n = (t.flags & 128) !== 0, i;
    if ((i = n) || (i = l !== null && l.memoizedState === null ? !1 : (xl.current & 2) !== 0), i && (u = !0, t.flags &= -129), i = (t.flags & 32) !== 0, t.flags &= -33, l === null) {
      if (I) {
        if (u ? ra(t) : ga(), (l = yl) ? (l = $o(
          l,
          jt
        ), l = l !== null && l.data !== "&" ? l : null, l !== null && (t.memoizedState = {
          dehydrated: l,
          treeContext: sa !== null ? { id: Yt, overflow: Bt } : null,
          retryLane: 536870912,
          hydrationErrors: null
        }, a = bs(l), a.return = t, t.child = a, Kl = t, yl = null)) : l = null, l === null) throw oa(t);
        return Ic(l) ? t.lanes = 32 : t.lanes = 536870912, null;
      }
      var c = e.children;
      return e = e.fallback, u ? (ga(), u = t.mode, c = zn(
        { mode: "hidden", children: c },
        u
      ), e = Ga(
        e,
        u,
        a,
        null
      ), c.return = t, e.return = t, c.sibling = e, t.child = c, e = t.child, e.memoizedState = yc(a), e.childLanes = rc(
        l,
        i,
        a
      ), t.memoizedState = vc, ou(null, e)) : (ra(t), gc(t, c));
    }
    var s = l.memoizedState;
    if (s !== null && (c = s.dehydrated, c !== null)) {
      if (n)
        t.flags & 256 ? (ra(t), t.flags &= -257, t = Sc(
          l,
          t,
          a
        )) : t.memoizedState !== null ? (ga(), t.child = l.child, t.flags |= 128, t = null) : (ga(), c = e.fallback, u = t.mode, e = zn(
          { mode: "visible", children: e.children },
          u
        ), c = Ga(
          c,
          u,
          a,
          null
        ), c.flags |= 2, e.return = t, c.return = t, e.sibling = c, t.child = e, Ja(
          t,
          l.child,
          null,
          a
        ), e = t.child, e.memoizedState = yc(a), e.childLanes = rc(
          l,
          i,
          a
        ), t.memoizedState = vc, t = ou(null, e));
      else if (ra(t), Ic(c)) {
        if (i = c.nextSibling && c.nextSibling.dataset, i) var y = i.dgst;
        i = y, e = Error(g(419)), e.stack = "", e.digest = i, Pe({ value: e, source: null, stack: null }), t = Sc(
          l,
          t,
          a
        );
      } else if (Ol || ye(l, t, a, !1), i = (a & l.childLanes) !== 0, Ol || i) {
        if (i = ml, i !== null && (e = jf(i, a), e !== 0 && e !== s.retryLane))
          throw s.retryLane = e, Ba(l, e), it(i, l, e), mc;
        Fc(c) || On(), t = Sc(
          l,
          t,
          a
        );
      } else
        Fc(c) ? (t.flags |= 192, t.child = l.child, t = null) : (l = s.treeContext, yl = Mt(
          c.nextSibling
        ), Kl = t, I = !0, da = null, jt = !1, l !== null && Es(t, l), t = gc(
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
    ) : (c = Ga(
      c,
      u,
      a,
      null
    ), c.flags |= 2), c.return = t, e.return = t, e.sibling = c, t.child = e, ou(null, e), e = t.child, c = l.child.memoizedState, c === null ? c = yc(a) : (u = c.cachePool, u !== null ? (s = Ml._currentValue, u = u.parent !== s ? { parent: s, pool: s } : u) : u = Ms(), c = {
      baseLanes: c.baseLanes | a,
      cachePool: u
    }), e.memoizedState = c, e.childLanes = rc(
      l,
      i,
      a
    ), t.memoizedState = vc, ou(l.child, e)) : (ra(t), a = l.child, l = a.sibling, a = Kt(a, {
      mode: "visible",
      children: e.children
    }), a.return = t, a.sibling = null, l !== null && (i = t.deletions, i === null ? (t.deletions = [l], t.flags |= 16) : i.push(l)), t.child = a, t.memoizedState = null, a);
  }
  function gc(l, t) {
    return t = zn(
      { mode: "visible", children: t },
      l.mode
    ), t.return = l, l.child = t;
  }
  function zn(l, t) {
    return l = dt(22, l, null, t), l.lanes = 0, l;
  }
  function Sc(l, t, a) {
    return Ja(t, l.child, null, a), l = gc(
      t,
      t.pendingProps.children
    ), l.flags |= 2, t.memoizedState = null, l;
  }
  function Qd(l, t, a) {
    l.lanes |= t;
    var e = l.alternate;
    e !== null && (e.lanes |= t), Ri(l.return, t, a);
  }
  function bc(l, t, a, e, u, n) {
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
  function Zd(l, t, a) {
    var e = t.pendingProps, u = e.revealOrder, n = e.tail;
    e = e.children;
    var i = xl.current, c = (i & 2) !== 0;
    if (c ? (i = i & 1 | 2, t.flags |= 128) : i &= 1, j(xl, i), wl(l, t, e, a), e = I ? Ie : 0, !c && l !== null && (l.flags & 128) !== 0)
      l: for (l = t.child; l !== null; ) {
        if (l.tag === 13)
          l.memoizedState !== null && Qd(l, a, t);
        else if (l.tag === 19)
          Qd(l, a, t);
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
          l = a.alternate, l !== null && sn(l) === null && (u = a), a = a.sibling;
        a = u, a === null ? (u = t.child, t.child = null) : (u = a.sibling, a.sibling = null), bc(
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
          if (l = u.alternate, l !== null && sn(l) === null) {
            t.child = u;
            break;
          }
          l = u.sibling, u.sibling = a, a = u, u = l;
        }
        bc(
          t,
          !0,
          a,
          null,
          n,
          e
        );
        break;
      case "together":
        bc(
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
        if (ye(
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
  function pc(l, t) {
    return (l.lanes & t) !== 0 ? !0 : (l = l.dependencies, !!(l !== null && ln(l)));
  }
  function kh(l, t, a) {
    switch (t.tag) {
      case 3:
        Hl(t, t.stateNode.containerInfo), ma(t, Ml, l.memoizedState.cache), Xa();
        break;
      case 27:
      case 5:
        qt(t);
        break;
      case 4:
        Hl(t, t.stateNode.containerInfo);
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
          return t.flags |= 128, Ki(t), null;
        break;
      case 13:
        var e = t.memoizedState;
        if (e !== null)
          return e.dehydrated !== null ? (ra(t), t.flags |= 128, null) : (a & t.child.childLanes) !== 0 ? Xd(l, t, a) : (ra(t), l = Ft(
            l,
            t,
            a
          ), l !== null ? l.sibling : null);
        ra(t);
        break;
      case 19:
        var u = (l.flags & 128) !== 0;
        if (e = (a & t.childLanes) !== 0, e || (ye(
          l,
          t,
          a,
          !1
        ), e = (a & t.childLanes) !== 0), u) {
          if (e)
            return Zd(
              l,
              t,
              a
            );
          t.flags |= 128;
        }
        if (u = t.memoizedState, u !== null && (u.rendering = null, u.tail = null, u.lastEffect = null), j(xl, xl.current), e) break;
        return null;
      case 22:
        return t.lanes = 0, Cd(
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
  function Ld(l, t, a) {
    if (l !== null)
      if (l.memoizedProps !== t.pendingProps)
        Ol = !0;
      else {
        if (!pc(l, a) && (t.flags & 128) === 0)
          return Ol = !1, kh(
            l,
            t,
            a
          );
        Ol = (l.flags & 131072) !== 0;
      }
    else
      Ol = !1, I && (t.flags & 1048576) !== 0 && zs(t, Ie, t.index);
    switch (t.lanes = 0, t.tag) {
      case 16:
        l: {
          var e = t.pendingProps;
          if (l = Va(t.elementType), t.type = l, typeof l == "function")
            xi(l) ? (e = Wa(l, e), t.tag = 1, t = Bd(
              null,
              t,
              l,
              e,
              a
            )) : (t.tag = 0, t = hc(
              null,
              t,
              l,
              e,
              a
            ));
          else {
            if (l != null) {
              var u = l.$$typeof;
              if (u === Zl) {
                t.tag = 11, t = Dd(
                  null,
                  t,
                  l,
                  e,
                  a
                );
                break l;
              } else if (u === w) {
                t.tag = 14, t = Ud(
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
        return hc(
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
        ), Bd(
          l,
          t,
          e,
          u,
          a
        );
      case 3:
        l: {
          if (Hl(
            t,
            t.stateNode.containerInfo
          ), l === null) throw Error(g(387));
          e = t.pendingProps;
          var n = t.memoizedState;
          u = n.element, Xi(l, t), iu(t, e, null, a);
          var i = t.memoizedState;
          if (e = i.cache, ma(t, Ml, e), e !== n.cache && Ci(
            t,
            [Ml],
            a,
            !0
          ), nu(), e = i.element, n.isDehydrated)
            if (n = {
              element: e,
              isDehydrated: !1,
              cache: i.cache
            }, t.updateQueue.baseState = n, t.memoizedState = n, t.flags & 256) {
              t = Gd(
                l,
                t,
                e,
                a
              );
              break l;
            } else if (e !== u) {
              u = Tt(
                Error(g(424)),
                t
              ), Pe(u), t = Gd(
                l,
                t,
                e,
                a
              );
              break l;
            } else
              for (l = t.stateNode.containerInfo, l.nodeType === 9 ? l = l.body : l = l.nodeName === "HTML" ? l.ownerDocument.body : l, yl = Mt(l.firstChild), Kl = t, I = !0, da = null, jt = !0, a = Cs(
                t,
                null,
                e,
                a
              ), t.child = a; a; )
                a.flags = a.flags & -3 | 4096, a = a.sibling;
          else {
            if (Xa(), e === u) {
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
        return pn(l, t), l === null ? (a = tm(
          t.type,
          null,
          t.pendingProps,
          null
        )) ? t.memoizedState = a : I || (a = t.type, l = t.pendingProps, e = Yn(
          J.current
        ).createElement(a), e[Vl] = t, e[lt] = l, Wl(e, a, l), Gl(e), t.stateNode = e) : t.memoizedState = tm(
          t.type,
          l.memoizedProps,
          t.pendingProps,
          l.memoizedState
        ), null;
      case 27:
        return qt(t), l === null && I && (e = t.stateNode = Io(
          t.type,
          t.pendingProps,
          J.current
        ), Kl = t, jt = !0, u = yl, xa(t.type) ? (Pc = u, yl = Mt(e.firstChild)) : yl = u), wl(
          l,
          t,
          t.pendingProps.children,
          a
        ), pn(l, t), l === null && (t.flags |= 4194304), t.child;
      case 5:
        return l === null && I && ((u = e = yl) && (e = jv(
          e,
          t.type,
          t.pendingProps,
          jt
        ), e !== null ? (t.stateNode = e, Kl = t, yl = Mt(e.firstChild), jt = !1, u = !0) : u = !1), u || oa(t)), qt(t), u = t.type, n = t.pendingProps, i = l !== null ? l.memoizedProps : null, e = n.children, Wc(u, n) ? e = null : i !== null && Wc(u, i) && (t.flags |= 32), t.memoizedState !== null && (u = wi(
          l,
          t,
          Qh,
          null,
          null,
          a
        ), ju._currentValue = u), pn(l, t), wl(l, t, e, a), t.child;
      case 6:
        return l === null && I && ((l = a = yl) && (a = _v(
          a,
          t.pendingProps,
          jt
        ), a !== null ? (t.stateNode = a, Kl = t, yl = null, l = !0) : l = !1), l || oa(t)), null;
      case 13:
        return Xd(l, t, a);
      case 4:
        return Hl(
          t,
          t.stateNode.containerInfo
        ), e = t.pendingProps, l === null ? t.child = Ja(
          t,
          null,
          e,
          a
        ) : wl(l, t, e, a), t.child;
      case 11:
        return Dd(
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
        return u = t.type._context, e = t.pendingProps.children, Za(t), u = Jl(u), e = e(u), t.flags |= 1, wl(l, t, e, a), t.child;
      case 14:
        return Ud(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 15:
        return Rd(
          l,
          t,
          t.type,
          t.pendingProps,
          a
        );
      case 19:
        return Zd(l, t, a);
      case 31:
        return $h(l, t, a);
      case 22:
        return Cd(
          l,
          t,
          a,
          t.pendingProps
        );
      case 24:
        return Za(t), e = Jl(Ml), l === null ? (u = Yi(), u === null && (u = ml, n = Hi(), u.pooledCache = n, n.refCount++, n !== null && (u.pooledCacheLanes |= a), u = n), t.memoizedState = { parent: e, cache: u }, Gi(t), ma(t, Ml, u)) : ((l.lanes & a) !== 0 && (Xi(l, t), iu(t, null, null, a), nu()), u = l.memoizedState, n = t.memoizedState, u.parent !== e ? (u = { parent: e, cache: e }, t.memoizedState = u, t.lanes === 0 && (t.memoizedState = t.updateQueue.baseState = u), ma(t, Ml, e)) : (e = n.cache, ma(t, Ml, e), e !== u.cache && Ci(
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
  function zc(l, t, a, e, u) {
    if ((t = (l.mode & 32) !== 0) && (t = !1), t) {
      if (l.flags |= 16777216, (u & 335544128) === u)
        if (l.stateNode.complete) l.flags |= 8192;
        else if (go()) l.flags |= 8192;
        else
          throw Ka = un, Bi;
    } else l.flags &= -16777217;
  }
  function Vd(l, t) {
    if (t.type !== "stylesheet" || (t.state.loading & 4) !== 0)
      l.flags &= -16777217;
    else if (l.flags |= 16777216, !im(t))
      if (go()) l.flags |= 8192;
      else
        throw Ka = un, Bi;
  }
  function En(l, t) {
    t !== null && (l.flags |= 4), l.flags & 16384 && (t = l.tag !== 22 ? Tf() : 536870912, l.lanes |= t, _e |= t);
  }
  function mu(l, t) {
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
  function rl(l) {
    var t = l.alternate !== null && l.alternate.child === l.child, a = 0, e = 0;
    if (t)
      for (var u = l.child; u !== null; )
        a |= u.lanes | u.childLanes, e |= u.subtreeFlags & 65011712, e |= u.flags & 65011712, u.return = l, u = u.sibling;
    else
      for (u = l.child; u !== null; )
        a |= u.lanes | u.childLanes, e |= u.subtreeFlags, e |= u.flags, u.return = l, u = u.sibling;
    return l.subtreeFlags |= e, l.childLanes = a, t;
  }
  function Fh(l, t, a) {
    var e = t.pendingProps;
    switch (Ni(t), t.tag) {
      case 16:
      case 15:
      case 0:
      case 11:
      case 7:
      case 8:
      case 12:
      case 9:
      case 14:
        return rl(t), null;
      case 1:
        return rl(t), null;
      case 3:
        return a = t.stateNode, e = null, l !== null && (e = l.memoizedState.cache), t.memoizedState.cache !== e && (t.flags |= 2048), Wt(Ml), fl(), a.pendingContext && (a.context = a.pendingContext, a.pendingContext = null), (l === null || l.child === null) && (ve(t) ? It(t) : l === null || l.memoizedState.isDehydrated && (t.flags & 256) === 0 || (t.flags |= 1024, Di())), rl(t), null;
      case 26:
        var u = t.type, n = t.memoizedState;
        return l === null ? (It(t), n !== null ? (rl(t), Vd(t, n)) : (rl(t), zc(
          t,
          u,
          null,
          e,
          a
        ))) : n ? n !== l.memoizedState ? (It(t), rl(t), Vd(t, n)) : (rl(t), t.flags &= -16777217) : (l = l.memoizedProps, l !== e && It(t), rl(t), zc(
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
            return rl(t), null;
          }
          l = O.current, ve(t) ? Ts(t) : (l = Io(u, e, a), t.stateNode = l, It(t));
        }
        return rl(t), null;
      case 5:
        if (bt(t), u = t.type, l !== null && t.stateNode != null)
          l.memoizedProps !== e && It(t);
        else {
          if (!e) {
            if (t.stateNode === null)
              throw Error(g(166));
            return rl(t), null;
          }
          if (n = O.current, ve(t))
            Ts(t);
          else {
            var i = Yn(
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
            l: switch (Wl(n, u, e), u) {
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
        return rl(t), zc(
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
          if (l = J.current, ve(t)) {
            if (l = t.stateNode, a = t.memoizedProps, e = null, u = Kl, u !== null)
              switch (u.tag) {
                case 27:
                case 5:
                  e = u.memoizedProps;
              }
            l[Vl] = t, l = !!(l.nodeValue === a || e !== null && e.suppressHydrationWarning === !0 || Qo(l.nodeValue, a)), l || oa(t, !0);
          } else
            l = Yn(l).createTextNode(
              e
            ), l[Vl] = t, t.stateNode = l;
        }
        return rl(t), null;
      case 31:
        if (a = t.memoizedState, l === null || l.memoizedState !== null) {
          if (e = ve(t), a !== null) {
            if (l === null) {
              if (!e) throw Error(g(318));
              if (l = t.memoizedState, l = l !== null ? l.dehydrated : null, !l) throw Error(g(557));
              l[Vl] = t;
            } else
              Xa(), (t.flags & 128) === 0 && (t.memoizedState = null), t.flags |= 4;
            rl(t), l = !1;
          } else
            a = Di(), l !== null && l.memoizedState !== null && (l.memoizedState.hydrationErrors = a), l = !0;
          if (!l)
            return t.flags & 256 ? (mt(t), t) : (mt(t), null);
          if ((t.flags & 128) !== 0)
            throw Error(g(558));
        }
        return rl(t), null;
      case 13:
        if (e = t.memoizedState, l === null || l.memoizedState !== null && l.memoizedState.dehydrated !== null) {
          if (u = ve(t), e !== null && e.dehydrated !== null) {
            if (l === null) {
              if (!u) throw Error(g(318));
              if (u = t.memoizedState, u = u !== null ? u.dehydrated : null, !u) throw Error(g(317));
              u[Vl] = t;
            } else
              Xa(), (t.flags & 128) === 0 && (t.memoizedState = null), t.flags |= 4;
            rl(t), u = !1;
          } else
            u = Di(), l !== null && l.memoizedState !== null && (l.memoizedState.hydrationErrors = u), u = !0;
          if (!u)
            return t.flags & 256 ? (mt(t), t) : (mt(t), null);
        }
        return mt(t), (t.flags & 128) !== 0 ? (t.lanes = a, t) : (a = e !== null, l = l !== null && l.memoizedState !== null, a && (e = t.child, u = null, e.alternate !== null && e.alternate.memoizedState !== null && e.alternate.memoizedState.cachePool !== null && (u = e.alternate.memoizedState.cachePool.pool), n = null, e.memoizedState !== null && e.memoizedState.cachePool !== null && (n = e.memoizedState.cachePool.pool), n !== u && (e.flags |= 2048)), a !== l && a && (t.child.flags |= 8192), En(t, t.updateQueue), rl(t), null);
      case 4:
        return fl(), l === null && Lc(t.stateNode.containerInfo), rl(t), null;
      case 10:
        return Wt(t.type), rl(t), null;
      case 19:
        if (E(xl), e = t.memoizedState, e === null) return rl(t), null;
        if (u = (t.flags & 128) !== 0, n = e.rendering, n === null)
          if (u) mu(e, !1);
          else {
            if (Tl !== 0 || l !== null && (l.flags & 128) !== 0)
              for (l = t.child; l !== null; ) {
                if (n = sn(l), n !== null) {
                  for (t.flags |= 128, mu(e, !1), l = n.updateQueue, t.updateQueue = l, En(t, l), t.subtreeFlags = 0, l = a, a = t.child; a !== null; )
                    Ss(a, l), a = a.sibling;
                  return j(
                    xl,
                    xl.current & 1 | 2
                  ), I && Jt(t, e.treeForkCount), t.child;
                }
                l = l.sibling;
              }
            e.tail !== null && Fl() > _n && (t.flags |= 128, u = !0, mu(e, !1), t.lanes = 4194304);
          }
        else {
          if (!u)
            if (l = sn(n), l !== null) {
              if (t.flags |= 128, u = !0, l = l.updateQueue, t.updateQueue = l, En(t, l), mu(e, !0), e.tail === null && e.tailMode === "hidden" && !n.alternate && !I)
                return rl(t), null;
            } else
              2 * Fl() - e.renderingStartTime > _n && a !== 536870912 && (t.flags |= 128, u = !0, mu(e, !1), t.lanes = 4194304);
          e.isBackwards ? (n.sibling = t.child, t.child = n) : (l = e.last, l !== null ? l.sibling = n : t.child = n, e.last = n);
        }
        return e.tail !== null ? (l = e.tail, e.rendering = l, e.tail = l.sibling, e.renderingStartTime = Fl(), l.sibling = null, a = xl.current, j(
          xl,
          u ? a & 1 | 2 : a & 1
        ), I && Jt(t, e.treeForkCount), l) : (rl(t), null);
      case 22:
      case 23:
        return mt(t), Vi(), e = t.memoizedState !== null, l !== null ? l.memoizedState !== null !== e && (t.flags |= 8192) : e && (t.flags |= 8192), e ? (a & 536870912) !== 0 && (t.flags & 128) === 0 && (rl(t), t.subtreeFlags & 6 && (t.flags |= 8192)) : rl(t), a = t.updateQueue, a !== null && En(t, a.retryQueue), a = null, l !== null && l.memoizedState !== null && l.memoizedState.cachePool !== null && (a = l.memoizedState.cachePool.pool), e = null, t.memoizedState !== null && t.memoizedState.cachePool !== null && (e = t.memoizedState.cachePool.pool), e !== a && (t.flags |= 2048), l !== null && E(La), null;
      case 24:
        return a = null, l !== null && (a = l.memoizedState.cache), t.memoizedState.cache !== a && (t.flags |= 2048), Wt(Ml), rl(t), null;
      case 25:
        return null;
      case 30:
        return null;
    }
    throw Error(g(156, t.tag));
  }
  function Ih(l, t) {
    switch (Ni(t), t.tag) {
      case 1:
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 3:
        return Wt(Ml), fl(), l = t.flags, (l & 65536) !== 0 && (l & 128) === 0 ? (t.flags = l & -65537 | 128, t) : null;
      case 26:
      case 27:
      case 5:
        return bt(t), null;
      case 31:
        if (t.memoizedState !== null) {
          if (mt(t), t.alternate === null)
            throw Error(g(340));
          Xa();
        }
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 13:
        if (mt(t), l = t.memoizedState, l !== null && l.dehydrated !== null) {
          if (t.alternate === null)
            throw Error(g(340));
          Xa();
        }
        return l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 19:
        return E(xl), null;
      case 4:
        return fl(), null;
      case 10:
        return Wt(t.type), null;
      case 22:
      case 23:
        return mt(t), Vi(), l !== null && E(La), l = t.flags, l & 65536 ? (t.flags = l & -65537 | 128, t) : null;
      case 24:
        return Wt(Ml), null;
      case 25:
        return null;
      default:
        return null;
    }
  }
  function Kd(l, t) {
    switch (Ni(t), t.tag) {
      case 3:
        Wt(Ml), fl();
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
        E(xl);
        break;
      case 10:
        Wt(t.type);
        break;
      case 22:
      case 23:
        mt(t), Vi(), l !== null && E(La);
        break;
      case 24:
        Wt(Ml);
    }
  }
  function hu(l, t) {
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
      il(t, t.return, c);
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
                il(
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
      il(t, t.return, z);
    }
  }
  function Jd(l) {
    var t = l.updateQueue;
    if (t !== null) {
      var a = l.stateNode;
      try {
        qs(t, a);
      } catch (e) {
        il(l, l.return, e);
      }
    }
  }
  function wd(l, t, a) {
    a.props = Wa(
      l.type,
      l.memoizedProps
    ), a.state = l.memoizedState;
    try {
      a.componentWillUnmount();
    } catch (e) {
      il(l, t, e);
    }
  }
  function vu(l, t) {
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
      il(l, t, u);
    }
  }
  function Gt(l, t) {
    var a = l.ref, e = l.refCleanup;
    if (a !== null)
      if (typeof e == "function")
        try {
          e();
        } catch (u) {
          il(l, t, u);
        } finally {
          l.refCleanup = null, l = l.alternate, l != null && (l.refCleanup = null);
        }
      else if (typeof a == "function")
        try {
          a(null);
        } catch (u) {
          il(l, t, u);
        }
      else a.current = null;
  }
  function Wd(l) {
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
      il(l, l.return, u);
    }
  }
  function Ec(l, t, a) {
    try {
      var e = l.stateNode;
      pv(e, l.type, a, t), e[lt] = t;
    } catch (u) {
      il(l, l.return, u);
    }
  }
  function $d(l) {
    return l.tag === 5 || l.tag === 3 || l.tag === 26 || l.tag === 27 && xa(l.type) || l.tag === 4;
  }
  function Tc(l) {
    l: for (; ; ) {
      for (; l.sibling === null; ) {
        if (l.return === null || $d(l.return)) return null;
        l = l.return;
      }
      for (l.sibling.return = l.return, l = l.sibling; l.tag !== 5 && l.tag !== 6 && l.tag !== 18; ) {
        if (l.tag === 27 && xa(l.type) || l.flags & 2 || l.child === null || l.tag === 4) continue l;
        l.child.return = l, l = l.child;
      }
      if (!(l.flags & 2)) return l.stateNode;
    }
  }
  function Ac(l, t, a) {
    var e = l.tag;
    if (e === 5 || e === 6)
      l = l.stateNode, t ? (a.nodeType === 9 ? a.body : a.nodeName === "HTML" ? a.ownerDocument.body : a).insertBefore(l, t) : (t = a.nodeType === 9 ? a.body : a.nodeName === "HTML" ? a.ownerDocument.body : a, t.appendChild(l), a = a._reactRootContainer, a != null || t.onclick !== null || (t.onclick = Lt));
    else if (e !== 4 && (e === 27 && xa(l.type) && (a = l.stateNode, t = null), l = l.child, l !== null))
      for (Ac(l, t, a), l = l.sibling; l !== null; )
        Ac(l, t, a), l = l.sibling;
  }
  function Tn(l, t, a) {
    var e = l.tag;
    if (e === 5 || e === 6)
      l = l.stateNode, t ? a.insertBefore(l, t) : a.appendChild(l);
    else if (e !== 4 && (e === 27 && xa(l.type) && (a = l.stateNode), l = l.child, l !== null))
      for (Tn(l, t, a), l = l.sibling; l !== null; )
        Tn(l, t, a), l = l.sibling;
  }
  function kd(l) {
    var t = l.stateNode, a = l.memoizedProps;
    try {
      for (var e = l.type, u = t.attributes; u.length; )
        t.removeAttributeNode(u[0]);
      Wl(t, e, a), t[Vl] = l, t[lt] = a;
    } catch (n) {
      il(l, l.return, n);
    }
  }
  var Pt = !1, Dl = !1, xc = !1, Fd = typeof WeakSet == "function" ? WeakSet : Set, Xl = null;
  function Ph(l, t) {
    if (l = l.containerInfo, Jc = Vn, l = ss(l), Si(l)) {
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
            var i = 0, c = -1, s = -1, y = 0, z = 0, A = l, r = null;
            t: for (; ; ) {
              for (var S; A !== a || u !== 0 && A.nodeType !== 3 || (c = i + u), A !== n || e !== 0 && A.nodeType !== 3 || (s = i + e), A.nodeType === 3 && (i += A.nodeValue.length), (S = A.firstChild) !== null; )
                r = A, A = S;
              for (; ; ) {
                if (A === l) break t;
                if (r === a && ++y === u && (c = i), r === n && ++z === e && (s = i), (S = A.nextSibling) !== null) break;
                A = r, r = A.parentNode;
              }
              A = S;
            }
            a = c === -1 || s === -1 ? null : { start: c, end: s };
          } else a = null;
        }
      a = a || { start: 0, end: 0 };
    } else a = null;
    for (wc = { focusedElem: l, selectionRange: a }, Vn = !1, Xl = t; Xl !== null; )
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
                  il(
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
                  kc(l);
                else if (a === 1)
                  switch (l.nodeName) {
                    case "HEAD":
                    case "HTML":
                    case "BODY":
                      kc(l);
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
  function Id(l, t, a) {
    var e = a.flags;
    switch (a.tag) {
      case 0:
      case 11:
      case 15:
        ta(l, a), e & 4 && hu(5, a);
        break;
      case 1:
        if (ta(l, a), e & 4)
          if (l = a.stateNode, t === null)
            try {
              l.componentDidMount();
            } catch (i) {
              il(a, a.return, i);
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
              il(
                a,
                a.return,
                i
              );
            }
          }
        e & 64 && Jd(a), e & 512 && vu(a, a.return);
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
            qs(l, t);
          } catch (i) {
            il(a, a.return, i);
          }
        }
        break;
      case 27:
        t === null && e & 4 && kd(a);
      case 26:
      case 5:
        ta(l, a), t === null && e & 4 && Wd(a), e & 512 && vu(a, a.return);
        break;
      case 12:
        ta(l, a);
        break;
      case 31:
        ta(l, a), e & 4 && to(l, a);
        break;
      case 13:
        ta(l, a), e & 4 && ao(l, a), e & 64 && (l = a.memoizedState, l !== null && (l = l.dehydrated, l !== null && (a = fv.bind(
          null,
          a
        ), Mv(l, a))));
        break;
      case 22:
        if (e = a.memoizedState !== null || Pt, !e) {
          t = t !== null && t.memoizedState !== null || Dl, u = Pt;
          var n = Dl;
          Pt = e, (Dl = t) && !n ? aa(
            l,
            a,
            (a.subtreeFlags & 8772) !== 0
          ) : ta(l, a), Pt = u, Dl = n;
        }
        break;
      case 30:
        break;
      default:
        ta(l, a);
    }
  }
  function Pd(l) {
    var t = l.alternate;
    t !== null && (l.alternate = null, Pd(t)), l.child = null, l.deletions = null, l.sibling = null, l.tag === 5 && (t = l.stateNode, t !== null && ti(t)), l.stateNode = null, l.return = null, l.dependencies = null, l.memoizedProps = null, l.memoizedState = null, l.pendingProps = null, l.stateNode = null, l.updateQueue = null;
  }
  var pl = null, at = !1;
  function la(l, t, a) {
    for (a = a.child; a !== null; )
      lo(l, t, a), a = a.sibling;
  }
  function lo(l, t, a) {
    if (ct && typeof ct.onCommitFiberUnmount == "function")
      try {
        ct.onCommitFiberUnmount(Be, a);
      } catch {
      }
    switch (a.tag) {
      case 26:
        Dl || Gt(a, t), la(
          l,
          t,
          a
        ), a.memoizedState ? a.memoizedState.count-- : a.stateNode && (a = a.stateNode, a.parentNode.removeChild(a));
        break;
      case 27:
        Dl || Gt(a, t);
        var e = pl, u = at;
        xa(a.type) && (pl = a.stateNode, at = !1), la(
          l,
          t,
          a
        ), Tu(a.stateNode), pl = e, at = u;
        break;
      case 5:
        Dl || Gt(a, t);
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
              il(
                a,
                t,
                n
              );
            }
          else
            try {
              pl.removeChild(a.stateNode);
            } catch (n) {
              il(
                a,
                t,
                n
              );
            }
        break;
      case 18:
        pl !== null && (at ? (l = pl, wo(
          l.nodeType === 9 ? l.body : l.nodeName === "HTML" ? l.ownerDocument.body : l,
          a.stateNode
        ), He(l)) : wo(pl, a.stateNode));
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
        Sa(2, a, t), Dl || Sa(4, a, t), la(
          l,
          t,
          a
        );
        break;
      case 1:
        Dl || (Gt(a, t), e = a.stateNode, typeof e.componentWillUnmount == "function" && wd(
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
        Dl = (e = Dl) || a.memoizedState !== null, la(
          l,
          t,
          a
        ), Dl = e;
        break;
      default:
        la(
          l,
          t,
          a
        );
    }
  }
  function to(l, t) {
    if (t.memoizedState === null && (l = t.alternate, l !== null && (l = l.memoizedState, l !== null))) {
      l = l.dehydrated;
      try {
        He(l);
      } catch (a) {
        il(t, t.return, a);
      }
    }
  }
  function ao(l, t) {
    if (t.memoizedState === null && (l = t.alternate, l !== null && (l = l.memoizedState, l !== null && (l = l.dehydrated, l !== null))))
      try {
        He(l);
      } catch (a) {
        il(t, t.return, a);
      }
  }
  function lv(l) {
    switch (l.tag) {
      case 31:
      case 13:
      case 19:
        var t = l.stateNode;
        return t === null && (t = l.stateNode = new Fd()), t;
      case 22:
        return l = l.stateNode, t = l._retryCache, t === null && (t = l._retryCache = new Fd()), t;
      default:
        throw Error(g(435, l.tag));
    }
  }
  function An(l, t) {
    var a = lv(l);
    t.forEach(function(e) {
      if (!a.has(e)) {
        a.add(e);
        var u = sv.bind(null, l, e);
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
        lo(n, i, u), pl = null, at = !1, n = u.alternate, n !== null && (n.return = null), u.return = null;
      }
    if (t.subtreeFlags & 13886)
      for (t = t.child; t !== null; )
        eo(t, l), t = t.sibling;
  }
  var Ut = null;
  function eo(l, t) {
    var a = l.alternate, e = l.flags;
    switch (l.tag) {
      case 0:
      case 11:
      case 14:
      case 15:
        et(t, l), ut(l), e & 4 && (Sa(3, l, l.return), hu(3, l), Sa(5, l, l.return));
        break;
      case 1:
        et(t, l), ut(l), e & 512 && (Dl || a === null || Gt(a, a.return)), e & 64 && Pt && (l = l.updateQueue, l !== null && (e = l.callbacks, e !== null && (a = l.shared.hiddenCallbacks, l.shared.hiddenCallbacks = a === null ? e : a.concat(e))));
        break;
      case 26:
        var u = Ut;
        if (et(t, l), ut(l), e & 512 && (Dl || a === null || Gt(a, a.return)), e & 4) {
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
                      )), Wl(n, e, a), n[Vl] = l, Gl(n), e = n;
                      break l;
                    case "link":
                      var i = um(
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
                      n = u.createElement(e), Wl(n, e, a), u.head.appendChild(n);
                      break;
                    case "meta":
                      if (i = um(
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
                      n = u.createElement(e), Wl(n, e, a), u.head.appendChild(n);
                      break;
                    default:
                      throw Error(g(468, e));
                  }
                  n[Vl] = l, Gl(n), e = n;
                }
                l.stateNode = e;
              } else
                nm(
                  u,
                  l.type,
                  l.stateNode
                );
            else
              l.stateNode = em(
                u,
                e,
                l.memoizedProps
              );
          else
            n !== e ? (n === null ? a.stateNode !== null && (a = a.stateNode, a.parentNode.removeChild(a)) : n.count--, e === null ? nm(
              u,
              l.type,
              l.stateNode
            ) : em(
              u,
              e,
              l.memoizedProps
            )) : e === null && l.stateNode !== null && Ec(
              l,
              l.memoizedProps,
              a.memoizedProps
            );
        }
        break;
      case 27:
        et(t, l), ut(l), e & 512 && (Dl || a === null || Gt(a, a.return)), a !== null && e & 4 && Ec(
          l,
          l.memoizedProps,
          a.memoizedProps
        );
        break;
      case 5:
        if (et(t, l), ut(l), e & 512 && (Dl || a === null || Gt(a, a.return)), l.flags & 32) {
          u = l.stateNode;
          try {
            ue(u, "");
          } catch (D) {
            il(l, l.return, D);
          }
        }
        e & 4 && l.stateNode != null && (u = l.memoizedProps, Ec(
          l,
          u,
          a !== null ? a.memoizedProps : u
        )), e & 1024 && (xc = !0);
        break;
      case 6:
        if (et(t, l), ut(l), e & 4) {
          if (l.stateNode === null)
            throw Error(g(162));
          e = l.memoizedProps, a = l.stateNode;
          try {
            a.nodeValue = e;
          } catch (D) {
            il(l, l.return, D);
          }
        }
        break;
      case 3:
        if (Xn = null, u = Ut, Ut = Bn(t.containerInfo), et(t, l), Ut = u, ut(l), e & 4 && a !== null && a.memoizedState.isDehydrated)
          try {
            He(t.containerInfo);
          } catch (D) {
            il(l, l.return, D);
          }
        xc && (xc = !1, uo(l));
        break;
      case 4:
        e = Ut, Ut = Bn(
          l.stateNode.containerInfo
        ), et(t, l), ut(l), Ut = e;
        break;
      case 12:
        et(t, l), ut(l);
        break;
      case 31:
        et(t, l), ut(l), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, An(l, e)));
        break;
      case 13:
        et(t, l), ut(l), l.child.flags & 8192 && l.memoizedState !== null != (a !== null && a.memoizedState !== null) && (jn = Fl()), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, An(l, e)));
        break;
      case 22:
        u = l.memoizedState !== null;
        var s = a !== null && a.memoizedState !== null, y = Pt, z = Dl;
        if (Pt = y || u, Dl = z || s, et(t, l), Dl = z, Pt = y, ut(l), e & 8192)
          l: for (t = l.stateNode, t._visibility = u ? t._visibility & -2 : t._visibility | 1, u && (a === null || s || Pt || Dl || $a(l)), a = null, t = l; ; ) {
            if (t.tag === 5 || t.tag === 26) {
              if (a === null) {
                s = a = t;
                try {
                  if (n = s.stateNode, u)
                    i = n.style, typeof i.setProperty == "function" ? i.setProperty("display", "none", "important") : i.display = "none";
                  else {
                    c = s.stateNode;
                    var A = s.memoizedProps.style, r = A != null && A.hasOwnProperty("display") ? A.display : null;
                    c.style.display = r == null || typeof r == "boolean" ? "" : ("" + r).trim();
                  }
                } catch (D) {
                  il(s, s.return, D);
                }
              }
            } else if (t.tag === 6) {
              if (a === null) {
                s = t;
                try {
                  s.stateNode.nodeValue = u ? "" : s.memoizedProps;
                } catch (D) {
                  il(s, s.return, D);
                }
              }
            } else if (t.tag === 18) {
              if (a === null) {
                s = t;
                try {
                  var S = s.stateNode;
                  u ? Wo(S, !0) : Wo(s.stateNode, !1);
                } catch (D) {
                  il(s, s.return, D);
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
        e & 4 && (e = l.updateQueue, e !== null && (a = e.retryQueue, a !== null && (e.retryQueue = null, An(l, a))));
        break;
      case 19:
        et(t, l), ut(l), e & 4 && (e = l.updateQueue, e !== null && (l.updateQueue = null, An(l, e)));
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
          if ($d(e)) {
            a = e;
            break;
          }
          e = e.return;
        }
        if (a == null) throw Error(g(160));
        switch (a.tag) {
          case 27:
            var u = a.stateNode, n = Tc(l);
            Tn(l, n, u);
            break;
          case 5:
            var i = a.stateNode;
            a.flags & 32 && (ue(i, ""), a.flags &= -33);
            var c = Tc(l);
            Tn(l, c, i);
            break;
          case 3:
          case 4:
            var s = a.stateNode.containerInfo, y = Tc(l);
            Ac(
              l,
              y,
              s
            );
            break;
          default:
            throw Error(g(161));
        }
      } catch (z) {
        il(l, l.return, z);
      }
      l.flags &= -3;
    }
    t & 4096 && (l.flags &= -4097);
  }
  function uo(l) {
    if (l.subtreeFlags & 1024)
      for (l = l.child; l !== null; ) {
        var t = l;
        uo(t), t.tag === 5 && t.flags & 1024 && t.stateNode.reset(), l = l.sibling;
      }
  }
  function ta(l, t) {
    if (t.subtreeFlags & 8772)
      for (t = t.child; t !== null; )
        Id(l, t.alternate, t), t = t.sibling;
  }
  function $a(l) {
    for (l = l.child; l !== null; ) {
      var t = l;
      switch (t.tag) {
        case 0:
        case 11:
        case 14:
        case 15:
          Sa(4, t, t.return), $a(t);
          break;
        case 1:
          Gt(t, t.return);
          var a = t.stateNode;
          typeof a.componentWillUnmount == "function" && wd(
            t,
            t.return,
            a
          ), $a(t);
          break;
        case 27:
          Tu(t.stateNode);
        case 26:
        case 5:
          Gt(t, t.return), $a(t);
          break;
        case 22:
          t.memoizedState === null && $a(t);
          break;
        case 30:
          $a(t);
          break;
        default:
          $a(t);
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
          ), hu(4, n);
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
              il(e, e.return, y);
            }
          if (e = n, u = e.updateQueue, u !== null) {
            var c = e.stateNode;
            try {
              var s = u.shared.hiddenCallbacks;
              if (s !== null)
                for (u.shared.hiddenCallbacks = null, u = 0; u < s.length; u++)
                  Hs(s[u], c);
            } catch (y) {
              il(e, e.return, y);
            }
          }
          a && i & 64 && Jd(n), vu(n, n.return);
          break;
        case 27:
          kd(n);
        case 26:
        case 5:
          aa(
            u,
            n,
            a
          ), a && e === null && i & 4 && Wd(n), vu(n, n.return);
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
          ), a && i & 4 && to(u, n);
          break;
        case 13:
          aa(
            u,
            n,
            a
          ), a && i & 4 && ao(u, n);
          break;
        case 22:
          n.memoizedState === null && aa(
            u,
            n,
            a
          ), vu(n, n.return);
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
  function jc(l, t) {
    var a = null;
    l !== null && l.memoizedState !== null && l.memoizedState.cachePool !== null && (a = l.memoizedState.cachePool.pool), l = null, t.memoizedState !== null && t.memoizedState.cachePool !== null && (l = t.memoizedState.cachePool.pool), l !== a && (l != null && l.refCount++, a != null && lu(a));
  }
  function _c(l, t) {
    l = null, t.alternate !== null && (l = t.alternate.memoizedState.cache), t = t.memoizedState.cache, t !== l && (t.refCount++, l != null && lu(l));
  }
  function Rt(l, t, a, e) {
    if (t.subtreeFlags & 10256)
      for (t = t.child; t !== null; )
        no(
          l,
          t,
          a,
          e
        ), t = t.sibling;
  }
  function no(l, t, a, e) {
    var u = t.flags;
    switch (t.tag) {
      case 0:
      case 11:
      case 15:
        Rt(
          l,
          t,
          a,
          e
        ), u & 2048 && hu(9, t);
        break;
      case 1:
        Rt(
          l,
          t,
          a,
          e
        );
        break;
      case 3:
        Rt(
          l,
          t,
          a,
          e
        ), u & 2048 && (l = null, t.alternate !== null && (l = t.alternate.memoizedState.cache), t = t.memoizedState.cache, t !== l && (t.refCount++, l != null && lu(l)));
        break;
      case 12:
        if (u & 2048) {
          Rt(
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
            il(t, t.return, s);
          }
        } else
          Rt(
            l,
            t,
            a,
            e
          );
        break;
      case 31:
        Rt(
          l,
          t,
          a,
          e
        );
        break;
      case 13:
        Rt(
          l,
          t,
          a,
          e
        );
        break;
      case 23:
        break;
      case 22:
        n = t.stateNode, i = t.alternate, t.memoizedState !== null ? n._visibility & 2 ? Rt(
          l,
          t,
          a,
          e
        ) : yu(l, t) : n._visibility & 2 ? Rt(
          l,
          t,
          a,
          e
        ) : (n._visibility |= 2, Ae(
          l,
          t,
          a,
          e,
          (t.subtreeFlags & 10256) !== 0 || !1
        )), u & 2048 && jc(i, t);
        break;
      case 24:
        Rt(
          l,
          t,
          a,
          e
        ), u & 2048 && _c(t.alternate, t);
        break;
      default:
        Rt(
          l,
          t,
          a,
          e
        );
    }
  }
  function Ae(l, t, a, e, u) {
    for (u = u && ((t.subtreeFlags & 10256) !== 0 || !1), t = t.child; t !== null; ) {
      var n = l, i = t, c = a, s = e, y = i.flags;
      switch (i.tag) {
        case 0:
        case 11:
        case 15:
          Ae(
            n,
            i,
            c,
            s,
            u
          ), hu(8, i);
          break;
        case 23:
          break;
        case 22:
          var z = i.stateNode;
          i.memoizedState !== null ? z._visibility & 2 ? Ae(
            n,
            i,
            c,
            s,
            u
          ) : yu(
            n,
            i
          ) : (z._visibility |= 2, Ae(
            n,
            i,
            c,
            s,
            u
          )), u && y & 2048 && jc(
            i.alternate,
            i
          );
          break;
        case 24:
          Ae(
            n,
            i,
            c,
            s,
            u
          ), u && y & 2048 && _c(i.alternate, i);
          break;
        default:
          Ae(
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
  function yu(l, t) {
    if (t.subtreeFlags & 10256)
      for (t = t.child; t !== null; ) {
        var a = l, e = t, u = e.flags;
        switch (e.tag) {
          case 22:
            yu(a, e), u & 2048 && jc(
              e.alternate,
              e
            );
            break;
          case 24:
            yu(a, e), u & 2048 && _c(e.alternate, e);
            break;
          default:
            yu(a, e);
        }
        t = t.sibling;
      }
  }
  var ru = 8192;
  function xe(l, t, a) {
    if (l.subtreeFlags & ru)
      for (l = l.child; l !== null; )
        io(
          l,
          t,
          a
        ), l = l.sibling;
  }
  function io(l, t, a) {
    switch (l.tag) {
      case 26:
        xe(
          l,
          t,
          a
        ), l.flags & ru && l.memoizedState !== null && Xv(
          a,
          Ut,
          l.memoizedState,
          l.memoizedProps
        );
        break;
      case 5:
        xe(
          l,
          t,
          a
        );
        break;
      case 3:
      case 4:
        var e = Ut;
        Ut = Bn(l.stateNode.containerInfo), xe(
          l,
          t,
          a
        ), Ut = e;
        break;
      case 22:
        l.memoizedState === null && (e = l.alternate, e !== null && e.memoizedState !== null ? (e = ru, ru = 16777216, xe(
          l,
          t,
          a
        ), ru = e) : xe(
          l,
          t,
          a
        ));
        break;
      default:
        xe(
          l,
          t,
          a
        );
    }
  }
  function co(l) {
    var t = l.alternate;
    if (t !== null && (l = t.child, l !== null)) {
      t.child = null;
      do
        t = l.sibling, l.sibling = null, l = t;
      while (l !== null);
    }
  }
  function gu(l) {
    var t = l.deletions;
    if ((l.flags & 16) !== 0) {
      if (t !== null)
        for (var a = 0; a < t.length; a++) {
          var e = t[a];
          Xl = e, so(
            e,
            l
          );
        }
      co(l);
    }
    if (l.subtreeFlags & 10256)
      for (l = l.child; l !== null; )
        fo(l), l = l.sibling;
  }
  function fo(l) {
    switch (l.tag) {
      case 0:
      case 11:
      case 15:
        gu(l), l.flags & 2048 && Sa(9, l, l.return);
        break;
      case 3:
        gu(l);
        break;
      case 12:
        gu(l);
        break;
      case 22:
        var t = l.stateNode;
        l.memoizedState !== null && t._visibility & 2 && (l.return === null || l.return.tag !== 13) ? (t._visibility &= -3, xn(l)) : gu(l);
        break;
      default:
        gu(l);
    }
  }
  function xn(l) {
    var t = l.deletions;
    if ((l.flags & 16) !== 0) {
      if (t !== null)
        for (var a = 0; a < t.length; a++) {
          var e = t[a];
          Xl = e, so(
            e,
            l
          );
        }
      co(l);
    }
    for (l = l.child; l !== null; ) {
      switch (t = l, t.tag) {
        case 0:
        case 11:
        case 15:
          Sa(8, t, t.return), xn(t);
          break;
        case 22:
          a = t.stateNode, a._visibility & 2 && (a._visibility &= -3, xn(t));
          break;
        default:
          xn(t);
      }
      l = l.sibling;
    }
  }
  function so(l, t) {
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
          lu(a.memoizedState.cache);
      }
      if (e = a.child, e !== null) e.return = a, Xl = e;
      else
        l: for (a = l; Xl !== null; ) {
          e = Xl;
          var u = e.sibling, n = e.return;
          if (Pd(e), e === a) {
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
  var tv = {
    getCacheForType: function(l) {
      var t = Jl(Ml), a = t.data.get(l);
      return a === void 0 && (a = l(), t.data.set(l, a)), a;
    },
    cacheSignal: function() {
      return Jl(Ml).controller.signal;
    }
  }, av = typeof WeakMap == "function" ? WeakMap : Map, ul = 0, ml = null, W = null, k = 0, nl = 0, ht = null, ba = !1, je = !1, Mc = !1, ea = 0, Tl = 0, pa = 0, ka = 0, Nc = 0, vt = 0, _e = 0, Su = null, nt = null, Oc = !1, jn = 0, oo = 0, _n = 1 / 0, Mn = null, za = null, ql = 0, Ea = null, Me = null, ua = 0, Dc = 0, Uc = null, mo = null, bu = 0, Rc = null;
  function yt() {
    return (ul & 2) !== 0 && k !== 0 ? k & -k : b.T !== null ? Gc() : _f();
  }
  function ho() {
    if (vt === 0)
      if ((k & 536870912) === 0 || I) {
        var l = Hu;
        Hu <<= 1, (Hu & 3932160) === 0 && (Hu = 262144), vt = l;
      } else vt = 536870912;
    return l = ot.current, l !== null && (l.flags |= 32), vt;
  }
  function it(l, t, a) {
    (l === ml && (nl === 2 || nl === 9) || l.cancelPendingCommit !== null) && (Ne(l, 0), Ta(
      l,
      k,
      vt,
      !1
    )), Xe(l, a), ((ul & 2) === 0 || l !== ml) && (l === ml && ((ul & 2) === 0 && (ka |= a), Tl === 4 && Ta(
      l,
      k,
      vt,
      !1
    )), Xt(l));
  }
  function vo(l, t, a) {
    if ((ul & 6) !== 0) throw Error(g(327));
    var e = !a && (t & 127) === 0 && (t & l.expiredLanes) === 0 || Ge(l, t), u = e ? nv(l, t) : Hc(l, t, !0), n = e;
    do {
      if (u === 0) {
        je && !e && Ta(l, t, 0, !1);
        break;
      } else {
        if (a = l.current.alternate, n && !ev(a)) {
          u = Hc(l, t, !1), n = !1;
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
              u = Su;
              var s = c.current.memoizedState.isDehydrated;
              if (s && (Ne(c, i).flags |= 256), i = Hc(
                c,
                i,
                !1
              ), i !== 2) {
                if (Mc && !s) {
                  c.errorRecoveryDisabledLanes |= n, ka |= n, u = 4;
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
          Ne(l, 0), Ta(l, t, 0, !0);
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
              Ta(
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
          if ((t & 62914560) === t && (u = jn + 300 - Fl(), 10 < u)) {
            if (Ta(
              e,
              t,
              vt,
              !ba
            ), Yu(e, 0, !0) !== 0) break l;
            ua = t, e.timeoutHandle = Ko(
              yo.bind(
                null,
                e,
                a,
                nt,
                Mn,
                Oc,
                t,
                vt,
                ka,
                _e,
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
          yo(
            e,
            a,
            nt,
            Mn,
            Oc,
            t,
            vt,
            ka,
            _e,
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
  function yo(l, t, a, e, u, n, i, c, s, y, z, A, r, S) {
    if (l.timeoutHandle = -1, A = t.subtreeFlags, A & 8192 || (A & 16785408) === 16785408) {
      A = {
        stylesheets: null,
        count: 0,
        imgCount: 0,
        imgBytes: 0,
        suspenseyImages: [],
        waitingForImages: !0,
        waitingForViewTransition: !1,
        unsuspend: Lt
      }, io(
        t,
        n,
        A
      );
      var D = (n & 62914560) === n ? jn - Fl() : (n & 4194048) === n ? oo - Fl() : 0;
      if (D = Qv(
        A,
        D
      ), D !== null) {
        ua = n, l.cancelPendingCommit = D(
          To.bind(
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
            A,
            null,
            r,
            S
          )
        ), Ta(l, n, i, !y);
        return;
      }
    }
    To(
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
  function ev(l) {
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
  function Ta(l, t, a, e) {
    t &= ~Nc, t &= ~ka, l.suspendedLanes |= t, l.pingedLanes &= ~t, e && (l.warmLanes |= t), e = l.expirationTimes;
    for (var u = t; 0 < u; ) {
      var n = 31 - ft(u), i = 1 << n;
      e[n] = -1, u &= ~i;
    }
    a !== 0 && Af(l, a, t);
  }
  function Nn() {
    return (ul & 6) === 0 ? (pu(0), !1) : !0;
  }
  function Cc() {
    if (W !== null) {
      if (nl === 0)
        var l = W.return;
      else
        l = W, wt = Qa = null, ki(l), be = null, au = 0, l = W;
      for (; l !== null; )
        Kd(l.alternate, l), l = l.return;
      W = null;
    }
  }
  function Ne(l, t) {
    var a = l.timeoutHandle;
    a !== -1 && (l.timeoutHandle = -1, Tv(a)), a = l.cancelPendingCommit, a !== null && (l.cancelPendingCommit = null, a()), ua = 0, Cc(), ml = l, W = a = Kt(l.current, null), k = t, nl = 0, ht = null, ba = !1, je = Ge(l, t), Mc = !1, _e = vt = Nc = ka = pa = Tl = 0, nt = Su = null, Oc = !1, (t & 8) !== 0 && (t |= t & 32);
    var e = l.entangledLanes;
    if (e !== 0)
      for (l = l.entanglements, e &= t; 0 < e; ) {
        var u = 31 - ft(e), n = 1 << u;
        t |= l[u], e &= ~n;
      }
    return ea = t, $u(), a;
  }
  function ro(l, t) {
    L = null, b.H = du, t === Se || t === en ? (t = Ds(), nl = 3) : t === Bi ? (t = Ds(), nl = 4) : nl = t === mc ? 8 : t !== null && typeof t == "object" && typeof t.then == "function" ? 6 : 1, ht = t, W === null && (Tl = 1, Sn(
      l,
      Tt(t, l.current)
    ));
  }
  function go() {
    var l = ot.current;
    return l === null ? !0 : (k & 4194048) === k ? _t === null : (k & 62914560) === k || (k & 536870912) !== 0 ? l === _t : !1;
  }
  function So() {
    var l = b.H;
    return b.H = du, l === null ? du : l;
  }
  function bo() {
    var l = b.A;
    return b.A = tv, l;
  }
  function On() {
    Tl = 4, ba || (k & 4194048) !== k && ot.current !== null || (je = !0), (pa & 134217727) === 0 && (ka & 134217727) === 0 || ml === null || Ta(
      ml,
      k,
      vt,
      !1
    );
  }
  function Hc(l, t, a) {
    var e = ul;
    ul |= 2;
    var u = So(), n = bo();
    (ml !== l || k !== t) && (Mn = null, Ne(l, t)), t = !1;
    var i = Tl;
    l: do
      try {
        if (nl !== 0 && W !== null) {
          var c = W, s = ht;
          switch (nl) {
            case 8:
              Cc(), i = 6;
              break l;
            case 3:
            case 2:
            case 9:
            case 6:
              ot.current === null && (t = !0);
              var y = nl;
              if (nl = 0, ht = null, Oe(l, c, s, y), a && je) {
                i = 0;
                break l;
              }
              break;
            default:
              y = nl, nl = 0, ht = null, Oe(l, c, s, y);
          }
        }
        uv(), i = Tl;
        break;
      } catch (z) {
        ro(l, z);
      }
    while (!0);
    return t && l.shellSuspendCounter++, wt = Qa = null, ul = e, b.H = u, b.A = n, W === null && (ml = null, k = 0, $u()), i;
  }
  function uv() {
    for (; W !== null; ) po(W);
  }
  function nv(l, t) {
    var a = ul;
    ul |= 2;
    var e = So(), u = bo();
    ml !== l || k !== t ? (Mn = null, _n = Fl() + 500, Ne(l, t)) : je = Ge(
      l,
      t
    );
    l: do
      try {
        if (nl !== 0 && W !== null) {
          t = W;
          var n = ht;
          t: switch (nl) {
            case 1:
              nl = 0, ht = null, Oe(l, t, n, 1);
              break;
            case 2:
            case 9:
              if (Ns(n)) {
                nl = 0, ht = null, zo(t);
                break;
              }
              t = function() {
                nl !== 2 && nl !== 9 || ml !== l || (nl = 7), Xt(l);
              }, n.then(t, t);
              break l;
            case 3:
              nl = 7;
              break l;
            case 4:
              nl = 5;
              break l;
            case 7:
              Ns(n) ? (nl = 0, ht = null, zo(t)) : (nl = 0, ht = null, Oe(l, t, n, 7));
              break;
            case 5:
              var i = null;
              switch (W.tag) {
                case 26:
                  i = W.memoizedState;
                case 5:
                case 27:
                  var c = W;
                  if (i ? im(i) : c.stateNode.complete) {
                    nl = 0, ht = null;
                    var s = c.sibling;
                    if (s !== null) W = s;
                    else {
                      var y = c.return;
                      y !== null ? (W = y, Dn(y)) : W = null;
                    }
                    break t;
                  }
              }
              nl = 0, ht = null, Oe(l, t, n, 5);
              break;
            case 6:
              nl = 0, ht = null, Oe(l, t, n, 6);
              break;
            case 8:
              Cc(), Tl = 6;
              break l;
            default:
              throw Error(g(462));
          }
        }
        iv();
        break;
      } catch (z) {
        ro(l, z);
      }
    while (!0);
    return wt = Qa = null, b.H = e, b.A = u, ul = a, W !== null ? 0 : (ml = null, k = 0, $u(), Tl);
  }
  function iv() {
    for (; W !== null && !qe(); )
      po(W);
  }
  function po(l) {
    var t = Ld(l.alternate, l, ea);
    l.memoizedProps = l.pendingProps, t === null ? Dn(l) : W = t;
  }
  function zo(l) {
    var t = l, a = t.alternate;
    switch (t.tag) {
      case 15:
      case 0:
        t = Yd(
          a,
          t,
          t.pendingProps,
          t.type,
          void 0,
          k
        );
        break;
      case 11:
        t = Yd(
          a,
          t,
          t.pendingProps,
          t.type.render,
          t.ref,
          k
        );
        break;
      case 5:
        ki(t);
      default:
        Kd(a, t), t = W = Ss(t, ea), t = Ld(a, t, ea);
    }
    l.memoizedProps = l.pendingProps, t === null ? Dn(l) : W = t;
  }
  function Oe(l, t, a, e) {
    wt = Qa = null, ki(t), be = null, au = 0;
    var u = t.return;
    try {
      if (Wh(
        l,
        u,
        t,
        a,
        k
      )) {
        Tl = 1, Sn(
          l,
          Tt(a, l.current)
        ), W = null;
        return;
      }
    } catch (n) {
      if (u !== null) throw W = u, n;
      Tl = 1, Sn(
        l,
        Tt(a, l.current)
      ), W = null;
      return;
    }
    t.flags & 32768 ? (I || e === 1 ? l = !0 : je || (k & 536870912) !== 0 ? l = !1 : (ba = l = !0, (e === 2 || e === 9 || e === 3 || e === 6) && (e = ot.current, e !== null && e.tag === 13 && (e.flags |= 16384))), Eo(t, l)) : Dn(t);
  }
  function Dn(l) {
    var t = l;
    do {
      if ((t.flags & 32768) !== 0) {
        Eo(
          t,
          ba
        );
        return;
      }
      l = t.return;
      var a = Fh(
        t.alternate,
        t,
        ea
      );
      if (a !== null) {
        W = a;
        return;
      }
      if (t = t.sibling, t !== null) {
        W = t;
        return;
      }
      W = t = l;
    } while (t !== null);
    Tl === 0 && (Tl = 5);
  }
  function Eo(l, t) {
    do {
      var a = Ih(l.alternate, l);
      if (a !== null) {
        a.flags &= 32767, W = a;
        return;
      }
      if (a = l.return, a !== null && (a.flags |= 32768, a.subtreeFlags = 0, a.deletions = null), !t && (l = l.sibling, l !== null)) {
        W = l;
        return;
      }
      W = l = a;
    } while (l !== null);
    Tl = 6, W = null;
  }
  function To(l, t, a, e, u, n, i, c, s) {
    l.cancelPendingCommit = null;
    do
      Un();
    while (ql !== 0);
    if ((ul & 6) !== 0) throw Error(g(327));
    if (t !== null) {
      if (t === l.current) throw Error(g(177));
      if (n = t.lanes | t.childLanes, n |= Ti, Gm(
        l,
        a,
        n,
        i,
        c,
        s
      ), l === ml && (W = ml = null, k = 0), Me = t, Ea = l, ua = a, Dc = n, Uc = u, mo = e, (t.subtreeFlags & 10256) !== 0 || (t.flags & 10256) !== 0 ? (l.callbackNode = null, l.callbackPriority = 0, dv(Ru, function() {
        return Mo(), null;
      })) : (l.callbackNode = null, l.callbackPriority = 0), e = (t.flags & 13878) !== 0, (t.subtreeFlags & 13878) !== 0 || e) {
        e = b.T, b.T = null, u = _.p, _.p = 2, i = ul, ul |= 4;
        try {
          Ph(l, t, a);
        } finally {
          ul = i, _.p = u, b.T = e;
        }
      }
      ql = 1, Ao(), xo(), jo();
    }
  }
  function Ao() {
    if (ql === 1) {
      ql = 0;
      var l = Ea, t = Me, a = (t.flags & 13878) !== 0;
      if ((t.subtreeFlags & 13878) !== 0 || a) {
        a = b.T, b.T = null;
        var e = _.p;
        _.p = 2;
        var u = ul;
        ul |= 4;
        try {
          eo(t, l);
          var n = wc, i = ss(l.containerInfo), c = n.focusedElem, s = n.selectionRange;
          if (i !== c && c && c.ownerDocument && fs(
            c.ownerDocument.documentElement,
            c
          )) {
            if (s !== null && Si(c)) {
              var y = s.start, z = s.end;
              if (z === void 0 && (z = y), "selectionStart" in c)
                c.selectionStart = y, c.selectionEnd = Math.min(
                  z,
                  c.value.length
                );
              else {
                var A = c.ownerDocument || document, r = A && A.defaultView || window;
                if (r.getSelection) {
                  var S = r.getSelection(), D = c.textContent.length, B = Math.min(s.start, D), ol = s.end === void 0 ? B : Math.min(s.end, D);
                  !S.extend && B > ol && (i = ol, ol = B, B = i);
                  var h = cs(
                    c,
                    B
                  ), m = cs(
                    c,
                    ol
                  );
                  if (h && m && (S.rangeCount !== 1 || S.anchorNode !== h.node || S.anchorOffset !== h.offset || S.focusNode !== m.node || S.focusOffset !== m.offset)) {
                    var v = A.createRange();
                    v.setStart(h.node, h.offset), S.removeAllRanges(), B > ol ? (S.addRange(v), S.extend(m.node, m.offset)) : (v.setEnd(m.node, m.offset), S.addRange(v));
                  }
                }
              }
            }
            for (A = [], S = c; S = S.parentNode; )
              S.nodeType === 1 && A.push({
                element: S,
                left: S.scrollLeft,
                top: S.scrollTop
              });
            for (typeof c.focus == "function" && c.focus(), c = 0; c < A.length; c++) {
              var T = A[c];
              T.element.scrollLeft = T.left, T.element.scrollTop = T.top;
            }
          }
          Vn = !!Jc, wc = Jc = null;
        } finally {
          ul = u, _.p = e, b.T = a;
        }
      }
      l.current = t, ql = 2;
    }
  }
  function xo() {
    if (ql === 2) {
      ql = 0;
      var l = Ea, t = Me, a = (t.flags & 8772) !== 0;
      if ((t.subtreeFlags & 8772) !== 0 || a) {
        a = b.T, b.T = null;
        var e = _.p;
        _.p = 2;
        var u = ul;
        ul |= 4;
        try {
          Id(l, t.alternate, t);
        } finally {
          ul = u, _.p = e, b.T = a;
        }
      }
      ql = 3;
    }
  }
  function jo() {
    if (ql === 4 || ql === 3) {
      ql = 0, Ye();
      var l = Ea, t = Me, a = ua, e = mo;
      (t.subtreeFlags & 10256) !== 0 || (t.flags & 10256) !== 0 ? ql = 5 : (ql = 0, Me = Ea = null, _o(l, l.pendingLanes));
      var u = l.pendingLanes;
      if (u === 0 && (za = null), Pn(a), t = t.stateNode, ct && typeof ct.onCommitFiberRoot == "function")
        try {
          ct.onCommitFiberRoot(
            Be,
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
      (ua & 3) !== 0 && Un(), Xt(l), u = l.pendingLanes, (a & 261930) !== 0 && (u & 42) !== 0 ? l === Rc ? bu++ : (bu = 0, Rc = l) : bu = 0, pu(0);
    }
  }
  function _o(l, t) {
    (l.pooledCacheLanes &= t) === 0 && (t = l.pooledCache, t != null && (l.pooledCache = null, lu(t)));
  }
  function Un() {
    return Ao(), xo(), jo(), Mo();
  }
  function Mo() {
    if (ql !== 5) return !1;
    var l = Ea, t = Dc;
    Dc = 0;
    var a = Pn(ua), e = b.T, u = _.p;
    try {
      _.p = 32 > a ? 32 : a, b.T = null, a = Uc, Uc = null;
      var n = Ea, i = ua;
      if (ql = 0, Me = Ea = null, ua = 0, (ul & 6) !== 0) throw Error(g(331));
      var c = ul;
      if (ul |= 4, fo(n.current), no(
        n,
        n.current,
        i,
        a
      ), ul = c, pu(0, !1), ct && typeof ct.onPostCommitFiberRoot == "function")
        try {
          ct.onPostCommitFiberRoot(Be, n);
        } catch {
        }
      return !0;
    } finally {
      _.p = u, b.T = e, _o(l, t);
    }
  }
  function No(l, t, a) {
    t = Tt(a, t), t = oc(l.stateNode, t, 2), l = ya(l, t, 2), l !== null && (Xe(l, 2), Xt(l));
  }
  function il(l, t, a) {
    if (l.tag === 3)
      No(l, l, a);
    else
      for (; t !== null; ) {
        if (t.tag === 3) {
          No(
            t,
            l,
            a
          );
          break;
        } else if (t.tag === 1) {
          var e = t.stateNode;
          if (typeof t.type.getDerivedStateFromError == "function" || typeof e.componentDidCatch == "function" && (za === null || !za.has(e))) {
            l = Tt(a, l), a = Nd(2), e = ya(t, a, 2), e !== null && (Od(
              a,
              e,
              t,
              l
            ), Xe(e, 2), Xt(e));
            break;
          }
        }
        t = t.return;
      }
  }
  function qc(l, t, a) {
    var e = l.pingCache;
    if (e === null) {
      e = l.pingCache = new av();
      var u = /* @__PURE__ */ new Set();
      e.set(t, u);
    } else
      u = e.get(t), u === void 0 && (u = /* @__PURE__ */ new Set(), e.set(t, u));
    u.has(a) || (Mc = !0, u.add(a), l = cv.bind(null, l, t, a), t.then(l, l));
  }
  function cv(l, t, a) {
    var e = l.pingCache;
    e !== null && e.delete(t), l.pingedLanes |= l.suspendedLanes & a, l.warmLanes &= ~a, ml === l && (k & a) === a && (Tl === 4 || Tl === 3 && (k & 62914560) === k && 300 > Fl() - jn ? (ul & 2) === 0 && Ne(l, 0) : Nc |= a, _e === k && (_e = 0)), Xt(l);
  }
  function Oo(l, t) {
    t === 0 && (t = Tf()), l = Ba(l, t), l !== null && (Xe(l, t), Xt(l));
  }
  function fv(l) {
    var t = l.memoizedState, a = 0;
    t !== null && (a = t.retryLane), Oo(l, a);
  }
  function sv(l, t) {
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
    e !== null && e.delete(t), Oo(l, a);
  }
  function dv(l, t) {
    return el(l, t);
  }
  var Rn = null, De = null, Yc = !1, Cn = !1, Bc = !1, Aa = 0;
  function Xt(l) {
    l !== De && l.next === null && (De === null ? Rn = De = l : De = De.next = l), Cn = !0, Yc || (Yc = !0, mv());
  }
  function pu(l, t) {
    if (!Bc && Cn) {
      Bc = !0;
      do
        for (var a = !1, e = Rn; e !== null; ) {
          if (l !== 0) {
            var u = e.pendingLanes;
            if (u === 0) var n = 0;
            else {
              var i = e.suspendedLanes, c = e.pingedLanes;
              n = (1 << 31 - ft(42 | l) + 1) - 1, n &= u & ~(i & ~c), n = n & 201326741 ? n & 201326741 | 1 : n ? n | 2 : 0;
            }
            n !== 0 && (a = !0, Co(e, n));
          } else
            n = k, n = Yu(
              e,
              e === ml ? n : 0,
              e.cancelPendingCommit !== null || e.timeoutHandle !== -1
            ), (n & 3) === 0 || Ge(e, n) || (a = !0, Co(e, n));
          e = e.next;
        }
      while (a);
      Bc = !1;
    }
  }
  function ov() {
    Do();
  }
  function Do() {
    Cn = Yc = !1;
    var l = 0;
    Aa !== 0 && Ev() && (l = Aa);
    for (var t = Fl(), a = null, e = Rn; e !== null; ) {
      var u = e.next, n = Uo(e, t);
      n === 0 ? (e.next = null, a === null ? Rn = u : a.next = u, u === null && (De = a)) : (a = e, (l !== 0 || (n & 3) !== 0) && (Cn = !0)), e = u;
    }
    ql !== 0 && ql !== 5 || pu(l), Aa !== 0 && (Aa = 0);
  }
  function Uo(l, t) {
    for (var a = l.suspendedLanes, e = l.pingedLanes, u = l.expirationTimes, n = l.pendingLanes & -62914561; 0 < n; ) {
      var i = 31 - ft(n), c = 1 << i, s = u[i];
      s === -1 ? ((c & a) === 0 || (c & e) !== 0) && (u[i] = Bm(c, t)) : s <= t && (l.expiredLanes |= c), n &= ~c;
    }
    if (t = ml, a = k, a = Yu(
      l,
      l === t ? a : 0,
      l.cancelPendingCommit !== null || l.timeoutHandle !== -1
    ), e = l.callbackNode, a === 0 || l === t && (nl === 2 || nl === 9) || l.cancelPendingCommit !== null)
      return e !== null && e !== null && Ua(e), l.callbackNode = null, l.callbackPriority = 0;
    if ((a & 3) === 0 || Ge(l, a)) {
      if (t = a & -a, t === l.callbackPriority) return t;
      switch (e !== null && Ua(e), Pn(a)) {
        case 2:
        case 8:
          a = zf;
          break;
        case 32:
          a = Ru;
          break;
        case 268435456:
          a = Ef;
          break;
        default:
          a = Ru;
      }
      return e = Ro.bind(null, l), a = el(a, e), l.callbackPriority = t, l.callbackNode = a, t;
    }
    return e !== null && e !== null && Ua(e), l.callbackPriority = 2, l.callbackNode = null, 2;
  }
  function Ro(l, t) {
    if (ql !== 0 && ql !== 5)
      return l.callbackNode = null, l.callbackPriority = 0, null;
    var a = l.callbackNode;
    if (Un() && l.callbackNode !== a)
      return null;
    var e = k;
    return e = Yu(
      l,
      l === ml ? e : 0,
      l.cancelPendingCommit !== null || l.timeoutHandle !== -1
    ), e === 0 ? null : (vo(l, e, t), Uo(l, Fl()), l.callbackNode != null && l.callbackNode === a ? Ro.bind(null, l) : null);
  }
  function Co(l, t) {
    if (Un()) return null;
    vo(l, t, !0);
  }
  function mv() {
    Av(function() {
      (ul & 6) !== 0 ? el(
        pf,
        ov
      ) : Do();
    });
  }
  function Gc() {
    if (Aa === 0) {
      var l = re;
      l === 0 && (l = Cu, Cu <<= 1, (Cu & 261888) === 0 && (Cu = 256)), Aa = l;
    }
    return Aa;
  }
  function Ho(l) {
    return l == null || typeof l == "symbol" || typeof l == "boolean" ? null : typeof l == "function" ? l : Qu("" + l);
  }
  function qo(l, t) {
    var a = t.ownerDocument.createElement("input");
    return a.name = t.name, a.value = t.value, l.id && a.setAttribute("form", l.id), t.parentNode.insertBefore(a, t), l = new FormData(l), a.parentNode.removeChild(a), l;
  }
  function hv(l, t, a, e, u) {
    if (t === "submit" && a && a.stateNode === u) {
      var n = Ho(
        (u[lt] || null).action
      ), i = e.submitter;
      i && (t = (t = i[lt] || null) ? Ho(t.formAction) : i.getAttribute("formAction"), t !== null && (n = t, i = null));
      var c = new Ku(
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
                if (Aa !== 0) {
                  var s = i ? qo(u, i) : new FormData(u);
                  nc(
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
                typeof n == "function" && (c.preventDefault(), s = i ? qo(u, i) : new FormData(u), nc(
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
  for (var Xc = 0; Xc < Ei.length; Xc++) {
    var Qc = Ei[Xc], vv = Qc.toLowerCase(), yv = Qc[0].toUpperCase() + Qc.slice(1);
    Dt(
      vv,
      "on" + yv
    );
  }
  Dt(ms, "onAnimationEnd"), Dt(hs, "onAnimationIteration"), Dt(vs, "onAnimationStart"), Dt("dblclick", "onDoubleClick"), Dt("focusin", "onFocus"), Dt("focusout", "onBlur"), Dt(Dh, "onTransitionRun"), Dt(Uh, "onTransitionStart"), Dt(Rh, "onTransitionCancel"), Dt(ys, "onTransitionEnd"), ae("onMouseEnter", ["mouseout", "mouseover"]), ae("onMouseLeave", ["mouseout", "mouseover"]), ae("onPointerEnter", ["pointerout", "pointerover"]), ae("onPointerLeave", ["pointerout", "pointerover"]), Ca(
    "onChange",
    "change click focusin focusout input keydown keyup selectionchange".split(" ")
  ), Ca(
    "onSelect",
    "focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(
      " "
    )
  ), Ca("onBeforeInput", [
    "compositionend",
    "keypress",
    "textInput",
    "paste"
  ]), Ca(
    "onCompositionEnd",
    "compositionend focusout keydown keypress keyup mousedown".split(" ")
  ), Ca(
    "onCompositionStart",
    "compositionstart focusout keydown keypress keyup mousedown".split(" ")
  ), Ca(
    "onCompositionUpdate",
    "compositionupdate focusout keydown keypress keyup mousedown".split(" ")
  );
  var zu = "abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(
    " "
  ), rv = new Set(
    "beforetoggle cancel close invalid load scroll scrollend toggle".split(" ").concat(zu)
  );
  function Yo(l, t) {
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
  function $(l, t) {
    var a = t[li];
    a === void 0 && (a = t[li] = /* @__PURE__ */ new Set());
    var e = l + "__bubble";
    a.has(e) || (Bo(t, l, 2, !1), a.add(e));
  }
  function Zc(l, t, a) {
    var e = 0;
    t && (e |= 4), Bo(
      a,
      l,
      e,
      t
    );
  }
  var Hn = "_reactListening" + Math.random().toString(36).slice(2);
  function Lc(l) {
    if (!l[Hn]) {
      l[Hn] = !0, Of.forEach(function(a) {
        a !== "selectionchange" && (rv.has(a) || Zc(a, !1, l), Zc(a, !0, l));
      });
      var t = l.nodeType === 9 ? l : l.ownerDocument;
      t === null || t[Hn] || (t[Hn] = !0, Zc("selectionchange", !1, t));
    }
  }
  function Bo(l, t, a, e) {
    switch (hm(t)) {
      case 2:
        var u = Vv;
        break;
      case 8:
        u = Kv;
        break;
      default:
        u = uf;
    }
    a = u.bind(
      null,
      t,
      a,
      l
    ), u = void 0, !si || t !== "touchstart" && t !== "touchmove" && t !== "wheel" || (u = !0), e ? u !== void 0 ? l.addEventListener(t, a, {
      capture: !0,
      passive: u
    }) : l.addEventListener(t, a, !0) : u !== void 0 ? l.addEventListener(t, a, {
      passive: u
    }) : l.addEventListener(t, a, !1);
  }
  function Vc(l, t, a, e, u) {
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
            if (i = Pa(c), i === null) return;
            if (s = i.tag, s === 5 || s === 6 || s === 26 || s === 27) {
              e = n = i;
              continue l;
            }
            c = c.parentNode;
          }
        }
        e = e.return;
      }
    Zf(function() {
      var y = n, z = ci(a), A = [];
      l: {
        var r = rs.get(l);
        if (r !== void 0) {
          var S = Ku, D = l;
          switch (l) {
            case "keypress":
              if (Lu(a) === 0) break l;
            case "keydown":
            case "keyup":
              S = sh;
              break;
            case "focusin":
              D = "focus", S = hi;
              break;
            case "focusout":
              D = "blur", S = hi;
              break;
            case "beforeblur":
            case "afterblur":
              S = hi;
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
              S = Kf;
              break;
            case "drag":
            case "dragend":
            case "dragenter":
            case "dragexit":
            case "dragleave":
            case "dragover":
            case "dragstart":
            case "drop":
              S = Fm;
              break;
            case "touchcancel":
            case "touchend":
            case "touchmove":
            case "touchstart":
              S = mh;
              break;
            case ms:
            case hs:
            case vs:
              S = lh;
              break;
            case ys:
              S = vh;
              break;
            case "scroll":
            case "scrollend":
              S = $m;
              break;
            case "wheel":
              S = rh;
              break;
            case "copy":
            case "cut":
            case "paste":
              S = ah;
              break;
            case "gotpointercapture":
            case "lostpointercapture":
            case "pointercancel":
            case "pointerdown":
            case "pointermove":
            case "pointerout":
            case "pointerover":
            case "pointerup":
              S = wf;
              break;
            case "toggle":
            case "beforetoggle":
              S = Sh;
          }
          var B = (t & 4) !== 0, ol = !B && (l === "scroll" || l === "scrollend"), h = B ? r !== null ? r + "Capture" : null : r;
          B = [];
          for (var m = y, v; m !== null; ) {
            var T = m;
            if (v = T.stateNode, T = T.tag, T !== 5 && T !== 26 && T !== 27 || v === null || h === null || (T = Le(m, h), T != null && B.push(
              Eu(m, T, v)
            )), ol) break;
            m = m.return;
          }
          0 < B.length && (r = new S(
            r,
            D,
            null,
            a,
            z
          ), A.push({ event: r, listeners: B }));
        }
      }
      if ((t & 7) === 0) {
        l: {
          if (r = l === "mouseover" || l === "pointerover", S = l === "mouseout" || l === "pointerout", r && a !== ii && (D = a.relatedTarget || a.fromElement) && (Pa(D) || D[Ia]))
            break l;
          if ((S || r) && (r = z.window === z ? z : (r = z.ownerDocument) ? r.defaultView || r.parentWindow : window, S ? (D = a.relatedTarget || a.toElement, S = y, D = D ? Pa(D) : null, D !== null && (ol = G(D), B = D.tag, D !== ol || B !== 5 && B !== 27 && B !== 6) && (D = null)) : (S = null, D = y), S !== D)) {
            if (B = Kf, T = "onMouseLeave", h = "onMouseEnter", m = "mouse", (l === "pointerout" || l === "pointerover") && (B = wf, T = "onPointerLeave", h = "onPointerEnter", m = "pointer"), ol = S == null ? r : Ze(S), v = D == null ? r : Ze(D), r = new B(
              T,
              m + "leave",
              S,
              a,
              z
            ), r.target = ol, r.relatedTarget = v, T = null, Pa(z) === y && (B = new B(
              h,
              m + "enter",
              D,
              a,
              z
            ), B.target = v, B.relatedTarget = ol, T = B), ol = T, S && D)
              t: {
                for (B = gv, h = S, m = D, v = 0, T = h; T; T = B(T))
                  v++;
                T = 0;
                for (var H = m; H; H = B(H))
                  T++;
                for (; 0 < v - T; )
                  h = B(h), v--;
                for (; 0 < T - v; )
                  m = B(m), T--;
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
            S !== null && Go(
              A,
              r,
              S,
              B,
              !1
            ), D !== null && ol !== null && Go(
              A,
              ol,
              D,
              B,
              !0
            );
          }
        }
        l: {
          if (r = y ? Ze(y) : window, S = r.nodeName && r.nodeName.toLowerCase(), S === "select" || S === "input" && r.type === "file")
            var tl = ts;
          else if (Pf(r))
            if (as)
              tl = Mh;
            else {
              tl = jh;
              var U = xh;
            }
          else
            S = r.nodeName, !S || S.toLowerCase() !== "input" || r.type !== "checkbox" && r.type !== "radio" ? y && ni(y.elementType) && (tl = ts) : tl = _h;
          if (tl && (tl = tl(l, y))) {
            ls(
              A,
              tl,
              a,
              z
            );
            break l;
          }
          U && U(l, r, y), l === "focusout" && y && r.type === "number" && y.memoizedProps.value != null && ui(r, "number", r.value);
        }
        switch (U = y ? Ze(y) : window, l) {
          case "focusin":
            (Pf(U) || U.contentEditable === "true") && (fe = U, bi = y, Fe = null);
            break;
          case "focusout":
            Fe = bi = fe = null;
            break;
          case "mousedown":
            pi = !0;
            break;
          case "contextmenu":
          case "mouseup":
          case "dragend":
            pi = !1, ds(A, a, z);
            break;
          case "selectionchange":
            if (Oh) break;
          case "keydown":
          case "keyup":
            ds(A, a, z);
        }
        var V;
        if (yi)
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
          ce ? Ff(l, a) && (F = "onCompositionEnd") : l === "keydown" && a.keyCode === 229 && (F = "onCompositionStart");
        F && (Wf && a.locale !== "ko" && (ce || F !== "onCompositionStart" ? F === "onCompositionEnd" && ce && (V = Lf()) : (fa = z, di = "value" in fa ? fa.value : fa.textContent, ce = !0)), U = qn(y, F), 0 < U.length && (F = new Jf(
          F,
          l,
          null,
          a,
          z
        ), A.push({ event: F, listeners: U }), V ? F.data = V : (V = If(a), V !== null && (F.data = V)))), (V = ph ? zh(l, a) : Eh(l, a)) && (F = qn(y, "onBeforeInput"), 0 < F.length && (U = new Jf(
          "onBeforeInput",
          "beforeinput",
          null,
          a,
          z
        ), A.push({
          event: U,
          listeners: F
        }), U.data = V)), hv(
          A,
          l,
          y,
          a,
          z
        );
      }
      Yo(A, t);
    });
  }
  function Eu(l, t, a) {
    return {
      instance: l,
      listener: t,
      currentTarget: a
    };
  }
  function qn(l, t) {
    for (var a = t + "Capture", e = []; l !== null; ) {
      var u = l, n = u.stateNode;
      if (u = u.tag, u !== 5 && u !== 26 && u !== 27 || n === null || (u = Le(l, a), u != null && e.unshift(
        Eu(l, u, n)
      ), u = Le(l, t), u != null && e.push(
        Eu(l, u, n)
      )), l.tag === 3) return e;
      l = l.return;
    }
    return [];
  }
  function gv(l) {
    if (l === null) return null;
    do
      l = l.return;
    while (l && l.tag !== 5 && l.tag !== 27);
    return l || null;
  }
  function Go(l, t, a, e, u) {
    for (var n = t._reactName, i = []; a !== null && a !== e; ) {
      var c = a, s = c.alternate, y = c.stateNode;
      if (c = c.tag, s !== null && s === e) break;
      c !== 5 && c !== 26 && c !== 27 || y === null || (s = y, u ? (y = Le(a, n), y != null && i.unshift(
        Eu(a, y, s)
      )) : u || (y = Le(a, n), y != null && i.push(
        Eu(a, y, s)
      ))), a = a.return;
    }
    i.length !== 0 && l.push({ event: t, listeners: i });
  }
  var Sv = /\r\n?/g, bv = /\u0000|\uFFFD/g;
  function Xo(l) {
    return (typeof l == "string" ? l : "" + l).replace(Sv, `
`).replace(bv, "");
  }
  function Qo(l, t) {
    return t = Xo(t), Xo(l) === t;
  }
  function dl(l, t, a, e, u, n) {
    switch (a) {
      case "children":
        typeof e == "string" ? t === "body" || t === "textarea" && e === "" || ue(l, e) : (typeof e == "number" || typeof e == "bigint") && t !== "body" && ue(l, "" + e);
        break;
      case "className":
        Gu(l, "class", e);
        break;
      case "tabIndex":
        Gu(l, "tabindex", e);
        break;
      case "dir":
      case "role":
      case "viewBox":
      case "width":
      case "height":
        Gu(l, a, e);
        break;
      case "style":
        Xf(l, e, n);
        break;
      case "data":
        if (t !== "object") {
          Gu(l, "data", e);
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
          typeof n == "function" && (a === "formAction" ? (t !== "input" && dl(l, t, "name", u.name, u, null), dl(
            l,
            t,
            "formEncType",
            u.formEncType,
            u,
            null
          ), dl(
            l,
            t,
            "formMethod",
            u.formMethod,
            u,
            null
          ), dl(
            l,
            t,
            "formTarget",
            u.formTarget,
            u,
            null
          )) : (dl(l, t, "encType", u.encType, u, null), dl(l, t, "method", u.method, u, null), dl(l, t, "target", u.target, u, null)));
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
        e != null && $("scroll", l);
        break;
      case "onScrollEnd":
        e != null && $("scrollend", l);
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
        $("beforetoggle", l), $("toggle", l), Bu(l, "popover", e);
        break;
      case "xlinkActuate":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:actuate",
          e
        );
        break;
      case "xlinkArcrole":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:arcrole",
          e
        );
        break;
      case "xlinkRole":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:role",
          e
        );
        break;
      case "xlinkShow":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:show",
          e
        );
        break;
      case "xlinkTitle":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:title",
          e
        );
        break;
      case "xlinkType":
        Zt(
          l,
          "http://www.w3.org/1999/xlink",
          "xlink:type",
          e
        );
        break;
      case "xmlBase":
        Zt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:base",
          e
        );
        break;
      case "xmlLang":
        Zt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:lang",
          e
        );
        break;
      case "xmlSpace":
        Zt(
          l,
          "http://www.w3.org/XML/1998/namespace",
          "xml:space",
          e
        );
        break;
      case "is":
        Bu(l, "is", e);
        break;
      case "innerText":
      case "textContent":
        break;
      default:
        (!(2 < a.length) || a[0] !== "o" && a[0] !== "O" || a[1] !== "n" && a[1] !== "N") && (a = wm.get(a) || a, Bu(l, a, e));
    }
  }
  function Kc(l, t, a, e, u, n) {
    switch (a) {
      case "style":
        Xf(l, e, n);
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
        typeof e == "string" ? ue(l, e) : (typeof e == "number" || typeof e == "bigint") && ue(l, "" + e);
        break;
      case "onScroll":
        e != null && $("scroll", l);
        break;
      case "onScrollEnd":
        e != null && $("scrollend", l);
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
        if (!Df.hasOwnProperty(a))
          l: {
            if (a[0] === "o" && a[1] === "n" && (u = a.endsWith("Capture"), t = a.slice(2, u ? a.length - 7 : void 0), n = l[lt] || null, n = n != null ? n[a] : null, typeof n == "function" && l.removeEventListener(t, n, u), typeof e == "function")) {
              typeof n != "function" && n !== null && (a in l ? l[a] = null : l.hasAttribute(a) && l.removeAttribute(a)), l.addEventListener(t, e, u);
              break l;
            }
            a in l ? l[a] = e : e === !0 ? l.setAttribute(a, "") : Bu(l, a, e);
          }
    }
  }
  function Wl(l, t, a) {
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
        $("error", l), $("load", l);
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
                  dl(l, t, n, i, a, null);
              }
          }
        u && dl(l, t, "srcSet", a.srcSet, a, null), e && dl(l, t, "src", a.src, a, null);
        return;
      case "input":
        $("invalid", l);
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
                  dl(l, t, e, z, a, null);
              }
          }
        qf(
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
        $("invalid", l), e = i = n = null;
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
                dl(l, t, u, c, a, null);
            }
        t = n, a = i, l.multiple = !!e, t != null ? ee(l, !!e, t, !1) : a != null && ee(l, !!e, a, !0);
        return;
      case "textarea":
        $("invalid", l), n = u = e = null;
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
                dl(l, t, i, c, a, null);
            }
        Bf(l, e, u, n);
        return;
      case "option":
        for (s in a)
          a.hasOwnProperty(s) && (e = a[s], e != null) && (s === "selected" ? l.selected = e && typeof e != "function" && typeof e != "symbol" : dl(l, t, s, e, a, null));
        return;
      case "dialog":
        $("beforetoggle", l), $("toggle", l), $("cancel", l), $("close", l);
        break;
      case "iframe":
      case "object":
        $("load", l);
        break;
      case "video":
      case "audio":
        for (e = 0; e < zu.length; e++)
          $(zu[e], l);
        break;
      case "image":
        $("error", l), $("load", l);
        break;
      case "details":
        $("toggle", l);
        break;
      case "embed":
      case "source":
      case "link":
        $("error", l), $("load", l);
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
                dl(l, t, y, e, a, null);
            }
        return;
      default:
        if (ni(t)) {
          for (z in a)
            a.hasOwnProperty(z) && (e = a[z], e !== void 0 && Kc(
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
      a.hasOwnProperty(c) && (e = a[c], e != null && dl(l, t, c, e, a, null));
  }
  function pv(l, t, a, e) {
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
          var A = a[S];
          if (a.hasOwnProperty(S) && A != null)
            switch (S) {
              case "checked":
                break;
              case "value":
                break;
              case "defaultValue":
                s = A;
              default:
                e.hasOwnProperty(S) || dl(l, t, S, null, e, A);
            }
        }
        for (var r in e) {
          var S = e[r];
          if (A = a[r], e.hasOwnProperty(r) && (S != null || A != null))
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
                S !== A && dl(
                  l,
                  t,
                  r,
                  S,
                  e,
                  A
                );
            }
        }
        ei(
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
                e.hasOwnProperty(n) || dl(
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
                n !== s && dl(
                  l,
                  t,
                  u,
                  n,
                  e,
                  s
                );
            }
        t = c, a = i, e = S, r != null ? ee(l, !!a, r, !1) : !!e != !!a && (t != null ? ee(l, !!a, t, !0) : ee(l, !!a, a ? [] : "", !1));
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
                dl(l, t, c, null, e, u);
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
                u !== n && dl(l, t, i, u, e, n);
            }
        Yf(l, r, S);
        return;
      case "option":
        for (var D in a)
          r = a[D], a.hasOwnProperty(D) && r != null && !e.hasOwnProperty(D) && (D === "selected" ? l.selected = !1 : dl(
            l,
            t,
            D,
            null,
            e,
            r
          ));
        for (s in e)
          r = e[s], S = a[s], e.hasOwnProperty(s) && r !== S && (r != null || S != null) && (s === "selected" ? l.selected = r && typeof r != "function" && typeof r != "symbol" : dl(
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
          r = a[B], a.hasOwnProperty(B) && r != null && !e.hasOwnProperty(B) && dl(l, t, B, null, e, r);
        for (y in e)
          if (r = e[y], S = a[y], e.hasOwnProperty(y) && r !== S && (r != null || S != null))
            switch (y) {
              case "children":
              case "dangerouslySetInnerHTML":
                if (r != null)
                  throw Error(g(137, t));
                break;
              default:
                dl(
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
        if (ni(t)) {
          for (var ol in a)
            r = a[ol], a.hasOwnProperty(ol) && r !== void 0 && !e.hasOwnProperty(ol) && Kc(
              l,
              t,
              ol,
              void 0,
              e,
              r
            );
          for (z in e)
            r = e[z], S = a[z], !e.hasOwnProperty(z) || r === S || r === void 0 && S === void 0 || Kc(
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
      r = a[h], a.hasOwnProperty(h) && r != null && !e.hasOwnProperty(h) && dl(l, t, h, null, e, r);
    for (A in e)
      r = e[A], S = a[A], !e.hasOwnProperty(A) || r === S || r == null && S == null || dl(l, t, A, r, e, S);
  }
  function Zo(l) {
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
  function zv() {
    if (typeof performance.getEntriesByType == "function") {
      for (var l = 0, t = 0, a = performance.getEntriesByType("resource"), e = 0; e < a.length; e++) {
        var u = a[e], n = u.transferSize, i = u.initiatorType, c = u.duration;
        if (n && c && Zo(i)) {
          for (i = 0, c = u.responseEnd, e += 1; e < a.length; e++) {
            var s = a[e], y = s.startTime;
            if (y > c) break;
            var z = s.transferSize, A = s.initiatorType;
            z && Zo(A) && (s = s.responseEnd, i += z * (s < c ? 1 : (c - y) / (s - y)));
          }
          if (--e, t += 8 * (n + i) / (u.duration / 1e3), l++, 10 < l) break;
        }
      }
      if (0 < l) return t / l / 1e6;
    }
    return navigator.connection && (l = navigator.connection.downlink, typeof l == "number") ? l : 5;
  }
  var Jc = null, wc = null;
  function Yn(l) {
    return l.nodeType === 9 ? l : l.ownerDocument;
  }
  function Lo(l) {
    switch (l) {
      case "http://www.w3.org/2000/svg":
        return 1;
      case "http://www.w3.org/1998/Math/MathML":
        return 2;
      default:
        return 0;
    }
  }
  function Vo(l, t) {
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
  function Wc(l, t) {
    return l === "textarea" || l === "noscript" || typeof t.children == "string" || typeof t.children == "number" || typeof t.children == "bigint" || typeof t.dangerouslySetInnerHTML == "object" && t.dangerouslySetInnerHTML !== null && t.dangerouslySetInnerHTML.__html != null;
  }
  var $c = null;
  function Ev() {
    var l = window.event;
    return l && l.type === "popstate" ? l === $c ? !1 : ($c = l, !0) : ($c = null, !1);
  }
  var Ko = typeof setTimeout == "function" ? setTimeout : void 0, Tv = typeof clearTimeout == "function" ? clearTimeout : void 0, Jo = typeof Promise == "function" ? Promise : void 0, Av = typeof queueMicrotask == "function" ? queueMicrotask : typeof Jo < "u" ? function(l) {
    return Jo.resolve(null).then(l).catch(xv);
  } : Ko;
  function xv(l) {
    setTimeout(function() {
      throw l;
    });
  }
  function xa(l) {
    return l === "head";
  }
  function wo(l, t) {
    var a = t, e = 0;
    do {
      var u = a.nextSibling;
      if (l.removeChild(a), u && u.nodeType === 8)
        if (a = u.data, a === "/$" || a === "/&") {
          if (e === 0) {
            l.removeChild(u), He(t);
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
    He(t);
  }
  function Wo(l, t) {
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
  function kc(l) {
    var t = l.firstChild;
    for (t && t.nodeType === 10 && (t = t.nextSibling); t; ) {
      var a = t;
      switch (t = t.nextSibling, a.nodeName) {
        case "HTML":
        case "HEAD":
        case "BODY":
          kc(a), ti(a);
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
  function jv(l, t, a, e) {
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
      if (l = Mt(l.nextSibling), l === null) break;
    }
    return null;
  }
  function _v(l, t, a) {
    if (t === "") return null;
    for (; l.nodeType !== 3; )
      if ((l.nodeType !== 1 || l.nodeName !== "INPUT" || l.type !== "hidden") && !a || (l = Mt(l.nextSibling), l === null)) return null;
    return l;
  }
  function $o(l, t) {
    for (; l.nodeType !== 8; )
      if ((l.nodeType !== 1 || l.nodeName !== "INPUT" || l.type !== "hidden") && !t || (l = Mt(l.nextSibling), l === null)) return null;
    return l;
  }
  function Fc(l) {
    return l.data === "$?" || l.data === "$~";
  }
  function Ic(l) {
    return l.data === "$!" || l.data === "$?" && l.ownerDocument.readyState !== "loading";
  }
  function Mv(l, t) {
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
  function Mt(l) {
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
  var Pc = null;
  function ko(l) {
    l = l.nextSibling;
    for (var t = 0; l; ) {
      if (l.nodeType === 8) {
        var a = l.data;
        if (a === "/$" || a === "/&") {
          if (t === 0)
            return Mt(l.nextSibling);
          t--;
        } else
          a !== "$" && a !== "$!" && a !== "$?" && a !== "$~" && a !== "&" || t++;
      }
      l = l.nextSibling;
    }
    return null;
  }
  function Fo(l) {
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
  function Io(l, t, a) {
    switch (t = Yn(a), l) {
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
    ti(l);
  }
  var Nt = /* @__PURE__ */ new Map(), Po = /* @__PURE__ */ new Set();
  function Bn(l) {
    return typeof l.getRootNode == "function" ? l.getRootNode() : l.nodeType === 9 ? l : l.ownerDocument;
  }
  var na = _.d;
  _.d = {
    f: Nv,
    r: Ov,
    D: Dv,
    C: Uv,
    L: Rv,
    m: Cv,
    X: qv,
    S: Hv,
    M: Yv
  };
  function Nv() {
    var l = na.f(), t = Nn();
    return l || t;
  }
  function Ov(l) {
    var t = le(l);
    t !== null && t.tag === 5 && t.type === "form" ? yd(t) : na.r(l);
  }
  var Ue = typeof document > "u" ? null : document;
  function lm(l, t, a) {
    var e = Ue;
    if (e && typeof t == "string" && t) {
      var u = zt(t);
      u = 'link[rel="' + l + '"][href="' + u + '"]', typeof a == "string" && (u += '[crossorigin="' + a + '"]'), Po.has(u) || (Po.add(u), l = { rel: l, crossOrigin: a, href: t }, e.querySelector(u) === null && (t = e.createElement("link"), Wl(t, "link", l), Gl(t), e.head.appendChild(t)));
    }
  }
  function Dv(l) {
    na.D(l), lm("dns-prefetch", l, null);
  }
  function Uv(l, t) {
    na.C(l, t), lm("preconnect", l, t);
  }
  function Rv(l, t, a) {
    na.L(l, t, a);
    var e = Ue;
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
          n = Re(l);
          break;
        case "script":
          n = Ce(l);
      }
      Nt.has(n) || (l = R(
        {
          rel: "preload",
          href: t === "image" && a && a.imageSrcSet ? void 0 : l,
          as: t
        },
        a
      ), Nt.set(n, l), e.querySelector(u) !== null || t === "style" && e.querySelector(Au(n)) || t === "script" && e.querySelector(xu(n)) || (t = e.createElement("link"), Wl(t, "link", l), Gl(t), e.head.appendChild(t)));
    }
  }
  function Cv(l, t) {
    na.m(l, t);
    var a = Ue;
    if (a && l) {
      var e = t && typeof t.as == "string" ? t.as : "script", u = 'link[rel="modulepreload"][as="' + zt(e) + '"][href="' + zt(l) + '"]', n = u;
      switch (e) {
        case "audioworklet":
        case "paintworklet":
        case "serviceworker":
        case "sharedworker":
        case "worker":
        case "script":
          n = Ce(l);
      }
      if (!Nt.has(n) && (l = R({ rel: "modulepreload", href: l }, t), Nt.set(n, l), a.querySelector(u) === null)) {
        switch (e) {
          case "audioworklet":
          case "paintworklet":
          case "serviceworker":
          case "sharedworker":
          case "worker":
          case "script":
            if (a.querySelector(xu(n)))
              return;
        }
        e = a.createElement("link"), Wl(e, "link", l), Gl(e), a.head.appendChild(e);
      }
    }
  }
  function Hv(l, t, a) {
    na.S(l, t, a);
    var e = Ue;
    if (e && l) {
      var u = te(e).hoistableStyles, n = Re(l);
      t = t || "default";
      var i = u.get(n);
      if (!i) {
        var c = { loading: 0, preload: null };
        if (i = e.querySelector(
          Au(n)
        ))
          c.loading = 5;
        else {
          l = R(
            { rel: "stylesheet", href: l, "data-precedence": t },
            a
          ), (a = Nt.get(n)) && lf(l, a);
          var s = i = e.createElement("link");
          Gl(s), Wl(s, "link", l), s._p = new Promise(function(y, z) {
            s.onload = y, s.onerror = z;
          }), s.addEventListener("load", function() {
            c.loading |= 1;
          }), s.addEventListener("error", function() {
            c.loading |= 2;
          }), c.loading |= 4, Gn(i, t, e);
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
  function qv(l, t) {
    na.X(l, t);
    var a = Ue;
    if (a && l) {
      var e = te(a).hoistableScripts, u = Ce(l), n = e.get(u);
      n || (n = a.querySelector(xu(u)), n || (l = R({ src: l, async: !0 }, t), (t = Nt.get(u)) && tf(l, t), n = a.createElement("script"), Gl(n), Wl(n, "link", l), a.head.appendChild(n)), n = {
        type: "script",
        instance: n,
        count: 1,
        state: null
      }, e.set(u, n));
    }
  }
  function Yv(l, t) {
    na.M(l, t);
    var a = Ue;
    if (a && l) {
      var e = te(a).hoistableScripts, u = Ce(l), n = e.get(u);
      n || (n = a.querySelector(xu(u)), n || (l = R({ src: l, async: !0, type: "module" }, t), (t = Nt.get(u)) && tf(l, t), n = a.createElement("script"), Gl(n), Wl(n, "link", l), a.head.appendChild(n)), n = {
        type: "script",
        instance: n,
        count: 1,
        state: null
      }, e.set(u, n));
    }
  }
  function tm(l, t, a, e) {
    var u = (u = J.current) ? Bn(u) : null;
    if (!u) throw Error(g(446));
    switch (l) {
      case "meta":
      case "title":
        return null;
      case "style":
        return typeof a.precedence == "string" && typeof a.href == "string" ? (t = Re(a.href), a = te(
          u
        ).hoistableStyles, e = a.get(t), e || (e = {
          type: "style",
          instance: null,
          count: 0,
          state: null
        }, a.set(t, e)), e) : { type: "void", instance: null, count: 0, state: null };
      case "link":
        if (a.rel === "stylesheet" && typeof a.href == "string" && typeof a.precedence == "string") {
          l = Re(a.href);
          var n = te(
            u
          ).hoistableStyles, i = n.get(l);
          if (i || (u = u.ownerDocument || u, i = {
            type: "stylesheet",
            instance: null,
            count: 0,
            state: { loading: 0, preload: null }
          }, n.set(l, i), (n = u.querySelector(
            Au(l)
          )) && !n._p && (i.instance = n, i.state.loading = 5), Nt.has(l) || (a = {
            rel: "preload",
            as: "style",
            href: a.href,
            crossOrigin: a.crossOrigin,
            integrity: a.integrity,
            media: a.media,
            hrefLang: a.hrefLang,
            referrerPolicy: a.referrerPolicy
          }, Nt.set(l, a), n || Bv(
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
        return t = a.async, a = a.src, typeof a == "string" && t && typeof t != "function" && typeof t != "symbol" ? (t = Ce(a), a = te(
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
  function Re(l) {
    return 'href="' + zt(l) + '"';
  }
  function Au(l) {
    return 'link[rel="stylesheet"][' + l + "]";
  }
  function am(l) {
    return R({}, l, {
      "data-precedence": l.precedence,
      precedence: null
    });
  }
  function Bv(l, t, a, e) {
    l.querySelector('link[rel="preload"][as="style"][' + t + "]") ? e.loading = 1 : (t = l.createElement("link"), e.preload = t, t.addEventListener("load", function() {
      return e.loading |= 1;
    }), t.addEventListener("error", function() {
      return e.loading |= 2;
    }), Wl(t, "link", a), Gl(t), l.head.appendChild(t));
  }
  function Ce(l) {
    return '[src="' + zt(l) + '"]';
  }
  function xu(l) {
    return "script[async]" + l;
  }
  function em(l, t, a) {
    if (t.count++, t.instance === null)
      switch (t.type) {
        case "style":
          var e = l.querySelector(
            'style[data-href~="' + zt(a.href) + '"]'
          );
          if (e)
            return t.instance = e, Gl(e), e;
          var u = R({}, a, {
            "data-href": a.href,
            "data-precedence": a.precedence,
            href: null,
            precedence: null
          });
          return e = (l.ownerDocument || l).createElement(
            "style"
          ), Gl(e), Wl(e, "style", u), Gn(e, a.precedence, l), t.instance = e;
        case "stylesheet":
          u = Re(a.href);
          var n = l.querySelector(
            Au(u)
          );
          if (n)
            return t.state.loading |= 4, t.instance = n, Gl(n), n;
          e = am(a), (u = Nt.get(u)) && lf(e, u), n = (l.ownerDocument || l).createElement("link"), Gl(n);
          var i = n;
          return i._p = new Promise(function(c, s) {
            i.onload = c, i.onerror = s;
          }), Wl(n, "link", e), t.state.loading |= 4, Gn(n, a.precedence, l), t.instance = n;
        case "script":
          return n = Ce(a.src), (u = l.querySelector(
            xu(n)
          )) ? (t.instance = u, Gl(u), u) : (e = a, (u = Nt.get(n)) && (e = R({}, a), tf(e, u)), l = l.ownerDocument || l, u = l.createElement("script"), Gl(u), Wl(u, "link", e), l.head.appendChild(u), t.instance = u);
        case "void":
          return null;
        default:
          throw Error(g(443, t.type));
      }
    else
      t.type === "stylesheet" && (t.state.loading & 4) === 0 && (e = t.instance, t.state.loading |= 4, Gn(e, a.precedence, l));
    return t.instance;
  }
  function Gn(l, t, a) {
    for (var e = a.querySelectorAll(
      'link[rel="stylesheet"][data-precedence],style[data-precedence]'
    ), u = e.length ? e[e.length - 1] : null, n = u, i = 0; i < e.length; i++) {
      var c = e[i];
      if (c.dataset.precedence === t) n = c;
      else if (n !== u) break;
    }
    n ? n.parentNode.insertBefore(l, n.nextSibling) : (t = a.nodeType === 9 ? a.head : a, t.insertBefore(l, t.firstChild));
  }
  function lf(l, t) {
    l.crossOrigin == null && (l.crossOrigin = t.crossOrigin), l.referrerPolicy == null && (l.referrerPolicy = t.referrerPolicy), l.title == null && (l.title = t.title);
  }
  function tf(l, t) {
    l.crossOrigin == null && (l.crossOrigin = t.crossOrigin), l.referrerPolicy == null && (l.referrerPolicy = t.referrerPolicy), l.integrity == null && (l.integrity = t.integrity);
  }
  var Xn = null;
  function um(l, t, a) {
    if (Xn === null) {
      var e = /* @__PURE__ */ new Map(), u = Xn = /* @__PURE__ */ new Map();
      u.set(a, e);
    } else
      u = Xn, e = u.get(a), e || (e = /* @__PURE__ */ new Map(), u.set(a, e));
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
  function nm(l, t, a) {
    l = l.ownerDocument || l, l.head.insertBefore(
      a,
      t === "title" ? l.querySelector("head > title") : null
    );
  }
  function Gv(l, t, a) {
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
  function im(l) {
    return !(l.type === "stylesheet" && (l.state.loading & 3) === 0);
  }
  function Xv(l, t, a, e) {
    if (a.type === "stylesheet" && (typeof e.media != "string" || matchMedia(e.media).matches !== !1) && (a.state.loading & 4) === 0) {
      if (a.instance === null) {
        var u = Re(e.href), n = t.querySelector(
          Au(u)
        );
        if (n) {
          t = n._p, t !== null && typeof t == "object" && typeof t.then == "function" && (l.count++, l = Qn.bind(l), t.then(l, l)), a.state.loading |= 4, a.instance = n, Gl(n);
          return;
        }
        n = t.ownerDocument || t, e = am(e), (u = Nt.get(u)) && lf(e, u), n = n.createElement("link"), Gl(n);
        var i = n;
        i._p = new Promise(function(c, s) {
          i.onload = c, i.onerror = s;
        }), Wl(n, "link", e), a.instance = n;
      }
      l.stylesheets === null && (l.stylesheets = /* @__PURE__ */ new Map()), l.stylesheets.set(a, t), (t = a.state.preload) && (a.state.loading & 3) === 0 && (l.count++, a = Qn.bind(l), t.addEventListener("load", a), t.addEventListener("error", a));
    }
  }
  var af = 0;
  function Qv(l, t) {
    return l.stylesheets && l.count === 0 && Ln(l, l.stylesheets), 0 < l.count || 0 < l.imgCount ? function(a) {
      var e = setTimeout(function() {
        if (l.stylesheets && Ln(l, l.stylesheets), l.unsuspend) {
          var n = l.unsuspend;
          l.unsuspend = null, n();
        }
      }, 6e4 + t);
      0 < l.imgBytes && af === 0 && (af = 62500 * zv());
      var u = setTimeout(
        function() {
          if (l.waitingForImages = !1, l.count === 0 && (l.stylesheets && Ln(l, l.stylesheets), l.unsuspend)) {
            var n = l.unsuspend;
            l.unsuspend = null, n();
          }
        },
        (l.imgBytes > af ? 50 : 800) + t
      );
      return l.unsuspend = a, function() {
        l.unsuspend = null, clearTimeout(e), clearTimeout(u);
      };
    } : null;
  }
  function Qn() {
    if (this.count--, this.count === 0 && (this.imgCount === 0 || !this.waitingForImages)) {
      if (this.stylesheets) Ln(this, this.stylesheets);
      else if (this.unsuspend) {
        var l = this.unsuspend;
        this.unsuspend = null, l();
      }
    }
  }
  var Zn = null;
  function Ln(l, t) {
    l.stylesheets = null, l.unsuspend !== null && (l.count++, Zn = /* @__PURE__ */ new Map(), t.forEach(Zv, l), Zn = null, Qn.call(l));
  }
  function Zv(l, t) {
    if (!(t.state.loading & 4)) {
      var a = Zn.get(l);
      if (a) var e = a.get(null);
      else {
        a = /* @__PURE__ */ new Map(), Zn.set(l, a);
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
  var ju = {
    $$typeof: Bl,
    Provider: null,
    Consumer: null,
    _currentValue: Y,
    _currentValue2: Y,
    _threadCount: 0
  };
  function Lv(l, t, a, e, u, n, i, c, s) {
    this.tag = 1, this.containerInfo = l, this.pingCache = this.current = this.pendingChildren = null, this.timeoutHandle = -1, this.callbackNode = this.next = this.pendingContext = this.context = this.cancelPendingCommit = null, this.callbackPriority = 0, this.expirationTimes = Fn(-1), this.entangledLanes = this.shellSuspendCounter = this.errorRecoveryDisabledLanes = this.expiredLanes = this.warmLanes = this.pingedLanes = this.suspendedLanes = this.pendingLanes = 0, this.entanglements = Fn(0), this.hiddenUpdates = Fn(null), this.identifierPrefix = e, this.onUncaughtError = u, this.onCaughtError = n, this.onRecoverableError = i, this.pooledCache = null, this.pooledCacheLanes = 0, this.formState = s, this.incompleteTransitions = /* @__PURE__ */ new Map();
  }
  function cm(l, t, a, e, u, n, i, c, s, y, z, A) {
    return l = new Lv(
      l,
      t,
      a,
      i,
      s,
      y,
      z,
      A,
      c
    ), t = 1, n === !0 && (t |= 24), n = dt(3, null, null, t), l.current = n, n.stateNode = l, t = Hi(), t.refCount++, l.pooledCache = t, t.refCount++, n.memoizedState = {
      element: e,
      isDehydrated: a,
      cache: t
    }, Gi(n), l;
  }
  function fm(l) {
    return l ? (l = oe, l) : oe;
  }
  function sm(l, t, a, e, u, n) {
    u = fm(u), e.context === null ? e.context = u : e.pendingContext = u, e = va(t), e.payload = { element: a }, n = n === void 0 ? null : n, n !== null && (e.callback = n), a = ya(l, e, t), a !== null && (it(a, l, t), uu(a, l, t));
  }
  function dm(l, t) {
    if (l = l.memoizedState, l !== null && l.dehydrated !== null) {
      var a = l.retryLane;
      l.retryLane = a !== 0 && a < t ? a : t;
    }
  }
  function ef(l, t) {
    dm(l, t), (l = l.alternate) && dm(l, t);
  }
  function om(l) {
    if (l.tag === 13 || l.tag === 31) {
      var t = Ba(l, 67108864);
      t !== null && it(t, l, 67108864), ef(l, 67108864);
    }
  }
  function mm(l) {
    if (l.tag === 13 || l.tag === 31) {
      var t = yt();
      t = In(t);
      var a = Ba(l, t);
      a !== null && it(a, l, t), ef(l, t);
    }
  }
  var Vn = !0;
  function Vv(l, t, a, e) {
    var u = b.T;
    b.T = null;
    var n = _.p;
    try {
      _.p = 2, uf(l, t, a, e);
    } finally {
      _.p = n, b.T = u;
    }
  }
  function Kv(l, t, a, e) {
    var u = b.T;
    b.T = null;
    var n = _.p;
    try {
      _.p = 8, uf(l, t, a, e);
    } finally {
      _.p = n, b.T = u;
    }
  }
  function uf(l, t, a, e) {
    if (Vn) {
      var u = nf(e);
      if (u === null)
        Vc(
          l,
          t,
          e,
          Kn,
          a
        ), vm(l, e);
      else if (wv(
        u,
        l,
        t,
        a,
        e
      ))
        e.stopPropagation();
      else if (vm(l, e), t & 4 && -1 < Jv.indexOf(l)) {
        for (; u !== null; ) {
          var n = le(u);
          if (n !== null)
            switch (n.tag) {
              case 3:
                if (n = n.stateNode, n.current.memoizedState.isDehydrated) {
                  var i = Ra(n.pendingLanes);
                  if (i !== 0) {
                    var c = n;
                    for (c.pendingLanes |= 2, c.entangledLanes |= 2; i; ) {
                      var s = 1 << 31 - ft(i);
                      c.entanglements[1] |= s, i &= ~s;
                    }
                    Xt(n), (ul & 6) === 0 && (_n = Fl() + 500, pu(0));
                  }
                }
                break;
              case 31:
              case 13:
                c = Ba(n, 2), c !== null && it(c, n, 2), Nn(), ef(n, 2);
            }
          if (n = nf(e), n === null && Vc(
            l,
            t,
            e,
            Kn,
            a
          ), n === u) break;
          u = n;
        }
        u !== null && e.stopPropagation();
      } else
        Vc(
          l,
          t,
          e,
          null,
          a
        );
    }
  }
  function nf(l) {
    return l = ci(l), cf(l);
  }
  var Kn = null;
  function cf(l) {
    if (Kn = null, l = Pa(l), l !== null) {
      var t = G(l);
      if (t === null) l = null;
      else {
        var a = t.tag;
        if (a === 13) {
          if (l = _l(t), l !== null) return l;
          l = null;
        } else if (a === 31) {
          if (l = hl(t), l !== null) return l;
          l = null;
        } else if (a === 3) {
          if (t.stateNode.current.memoizedState.isDehydrated)
            return t.tag === 3 ? t.stateNode.containerInfo : null;
          l = null;
        } else t !== l && (l = null);
      }
    }
    return Kn = l, null;
  }
  function hm(l) {
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
        switch (Dm()) {
          case pf:
            return 2;
          case zf:
            return 8;
          case Ru:
          case Um:
            return 32;
          case Ef:
            return 268435456;
          default:
            return 32;
        }
      default:
        return 32;
    }
  }
  var ff = !1, ja = null, _a = null, Ma = null, _u = /* @__PURE__ */ new Map(), Mu = /* @__PURE__ */ new Map(), Na = [], Jv = "mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset".split(
    " "
  );
  function vm(l, t) {
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
        Ma = null;
        break;
      case "pointerover":
      case "pointerout":
        _u.delete(t.pointerId);
        break;
      case "gotpointercapture":
      case "lostpointercapture":
        Mu.delete(t.pointerId);
    }
  }
  function Nu(l, t, a, e, u, n) {
    return l === null || l.nativeEvent !== n ? (l = {
      blockedOn: t,
      domEventName: a,
      eventSystemFlags: e,
      nativeEvent: n,
      targetContainers: [u]
    }, t !== null && (t = le(t), t !== null && om(t)), l) : (l.eventSystemFlags |= e, t = l.targetContainers, u !== null && t.indexOf(u) === -1 && t.push(u), l);
  }
  function wv(l, t, a, e, u) {
    switch (t) {
      case "focusin":
        return ja = Nu(
          ja,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "dragenter":
        return _a = Nu(
          _a,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "mouseover":
        return Ma = Nu(
          Ma,
          l,
          t,
          a,
          e,
          u
        ), !0;
      case "pointerover":
        var n = u.pointerId;
        return _u.set(
          n,
          Nu(
            _u.get(n) || null,
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
          Nu(
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
  function ym(l) {
    var t = Pa(l.target);
    if (t !== null) {
      var a = G(t);
      if (a !== null) {
        if (t = a.tag, t === 13) {
          if (t = _l(a), t !== null) {
            l.blockedOn = t, Mf(l.priority, function() {
              mm(a);
            });
            return;
          }
        } else if (t === 31) {
          if (t = hl(a), t !== null) {
            l.blockedOn = t, Mf(l.priority, function() {
              mm(a);
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
  function Jn(l) {
    if (l.blockedOn !== null) return !1;
    for (var t = l.targetContainers; 0 < t.length; ) {
      var a = nf(l.nativeEvent);
      if (a === null) {
        a = l.nativeEvent;
        var e = new a.constructor(
          a.type,
          a
        );
        ii = e, a.target.dispatchEvent(e), ii = null;
      } else
        return t = le(a), t !== null && om(t), l.blockedOn = a, !1;
      t.shift();
    }
    return !0;
  }
  function rm(l, t, a) {
    Jn(l) && a.delete(t);
  }
  function Wv() {
    ff = !1, ja !== null && Jn(ja) && (ja = null), _a !== null && Jn(_a) && (_a = null), Ma !== null && Jn(Ma) && (Ma = null), _u.forEach(rm), Mu.forEach(rm);
  }
  function wn(l, t) {
    l.blockedOn === t && (l.blockedOn = null, ff || (ff = !0, N.unstable_scheduleCallback(
      N.unstable_NormalPriority,
      Wv
    )));
  }
  var Wn = null;
  function gm(l) {
    Wn !== l && (Wn = l, N.unstable_scheduleCallback(
      N.unstable_NormalPriority,
      function() {
        Wn === l && (Wn = null);
        for (var t = 0; t < l.length; t += 3) {
          var a = l[t], e = l[t + 1], u = l[t + 2];
          if (typeof e != "function") {
            if (cf(e || a) === null)
              continue;
            break;
          }
          var n = le(a);
          n !== null && (l.splice(t, 3), t -= 3, nc(
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
  function He(l) {
    function t(s) {
      return wn(s, l);
    }
    ja !== null && wn(ja, l), _a !== null && wn(_a, l), Ma !== null && wn(Ma, l), _u.forEach(t), Mu.forEach(t);
    for (var a = 0; a < Na.length; a++) {
      var e = Na[a];
      e.blockedOn === l && (e.blockedOn = null);
    }
    for (; 0 < Na.length && (a = Na[0], a.blockedOn === null); )
      ym(a), a.blockedOn === null && Na.shift();
    if (a = (l.ownerDocument || l).$$reactFormReplay, a != null)
      for (e = 0; e < a.length; e += 3) {
        var u = a[e], n = a[e + 1], i = u[lt] || null;
        if (typeof n == "function")
          i || gm(a);
        else if (i) {
          var c = null;
          if (n && n.hasAttribute("formAction")) {
            if (u = n, i = n[lt] || null)
              c = i.formAction;
            else if (cf(u) !== null) continue;
          } else c = i.action;
          typeof c == "function" ? a[e + 1] = c : (a.splice(e, 3), e -= 3), gm(a);
        }
      }
  }
  function Sm() {
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
  function sf(l) {
    this._internalRoot = l;
  }
  $n.prototype.render = sf.prototype.render = function(l) {
    var t = this._internalRoot;
    if (t === null) throw Error(g(409));
    var a = t.current, e = yt();
    sm(a, e, l, t, null, null);
  }, $n.prototype.unmount = sf.prototype.unmount = function() {
    var l = this._internalRoot;
    if (l !== null) {
      this._internalRoot = null;
      var t = l.containerInfo;
      sm(l.current, 2, null, l, null, null), Nn(), t[Ia] = null;
    }
  };
  function $n(l) {
    this._internalRoot = l;
  }
  $n.prototype.unstable_scheduleHydration = function(l) {
    if (l) {
      var t = _f();
      l = { blockedOn: null, target: l, priority: t };
      for (var a = 0; a < Na.length && t !== 0 && t < Na[a].priority; a++) ;
      Na.splice(a, 0, l), a === 0 && ym(l);
    }
  };
  var bm = Rl.version;
  if (bm !== "19.2.8")
    throw Error(
      g(
        527,
        bm,
        "19.2.8"
      )
    );
  _.findDOMNode = function(l) {
    var t = l._reactInternals;
    if (t === void 0)
      throw typeof l.render == "function" ? Error(g(188)) : (l = Object.keys(l).join(","), Error(g(268, l)));
    return l = p(t), l = l !== null ? X(l) : null, l = l === null ? null : l.stateNode, l;
  };
  var $v = {
    bundleType: 0,
    version: "19.2.8",
    rendererPackageName: "react-dom",
    currentDispatcherRef: b,
    reconcilerVersion: "19.2.8"
  };
  if (typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u") {
    var kn = __REACT_DEVTOOLS_GLOBAL_HOOK__;
    if (!kn.isDisabled && kn.supportsFiber)
      try {
        Be = kn.inject(
          $v
        ), ct = kn;
      } catch {
      }
  }
  return Du.createRoot = function(l, t) {
    if (!gl(l)) throw Error(g(299));
    var a = !1, e = "", u = xd, n = jd, i = _d;
    return t != null && (t.unstable_strictMode === !0 && (a = !0), t.identifierPrefix !== void 0 && (e = t.identifierPrefix), t.onUncaughtError !== void 0 && (u = t.onUncaughtError), t.onCaughtError !== void 0 && (n = t.onCaughtError), t.onRecoverableError !== void 0 && (i = t.onRecoverableError)), t = cm(
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
      Sm
    ), l[Ia] = t.current, Lc(l), new sf(t);
  }, Du.hydrateRoot = function(l, t, a) {
    if (!gl(l)) throw Error(g(299));
    var e = !1, u = "", n = xd, i = jd, c = _d, s = null;
    return a != null && (a.unstable_strictMode === !0 && (e = !0), a.identifierPrefix !== void 0 && (u = a.identifierPrefix), a.onUncaughtError !== void 0 && (n = a.onUncaughtError), a.onCaughtError !== void 0 && (i = a.onCaughtError), a.onRecoverableError !== void 0 && (c = a.onRecoverableError), a.formState !== void 0 && (s = a.formState)), t = cm(
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
      Sm
    ), t.context = fm(null), a = t.current, e = yt(), e = In(e), u = va(e), u.callback = null, ya(a, u, e), a = e, t.current.lanes = a, Xe(t, a), Xt(t), l[Ia] = t.current, Lc(l), new $n(t);
  }, Du.version = "19.2.8", Du;
}
var Nm;
function n0() {
  if (Nm) return mf.exports;
  Nm = 1;
  function N() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function"))
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(N);
      } catch (Rl) {
        console.error(Rl);
      }
  }
  return N(), mf.exports = u0(), mf.exports;
}
var i0 = n0();
const Om = ["#ddebff", "#fff0c9", "#e8dfff", "#ddf4e5", "#ffe1e1", "#eee7ff"], rf = (N) => `${N}-${Date.now()}-${Math.random().toString(16).slice(2)}`, gf = (N) => N ? new Date(N.replace(" ", "T")).toLocaleTimeString("en-MY", { hour: "2-digit", minute: "2-digit", second: "2-digit" }) : "—";
function c0({ root: N }) {
  const [Rl, cl] = Ul.useState(!0), [g, gl] = Ul.useState(""), [G, _l] = Ul.useState("live"), [hl, C] = Ul.useState(null), [p, X] = Ul.useState({ width: 1e3, height: 590, zones: [], walls: [], assets: [], labels: [] }), [R, vl] = Ul.useState({ locations: [], subLocations: [], lanes: [], devices: [] }), [Al, Ql] = Ul.useState([]), [Cl, rt] = Ul.useState([]), [Yl, Ct] = Ul.useState({ design: N.dataset.canDesign === "1", publish: N.dataset.canPublish === "1" }), [Bl, Zl] = Ul.useState("select"), [Sl, bl] = Ul.useState(null), [w, Ll] = Ul.useState(null), [kl, Qt] = Ul.useState(1), [gt, zl] = Ul.useState(""), [Ht, St] = Ul.useState(!1), Pl = Ul.useRef(null), b = Ul.useRef(null);
  async function _() {
    try {
      const d = await fetch(N.dataset.bootstrapUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!d.ok) throw new Error(`Unable to load E-Map (${d.status})`);
      const M = (await d.json()).data;
      C(M.map), X(M.map.layout), vl(M.references), Ql(M.visitors || []), rt(M.movementLog || []), Ct(M.permissions || Yl), gl("");
    } catch (d) {
      gl(d.message || "Unable to load E-Map.");
    } finally {
      cl(!1);
    }
  }
  async function Y(d = !1) {
    try {
      const x = await fetch(N.dataset.liveUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!x.ok) throw new Error("Live movement refresh failed.");
      const M = await x.json();
      Ql(M.data.visitors || []), rt(M.data.movementLog || []), d || zl(`Live data refreshed at ${(/* @__PURE__ */ new Date()).toLocaleTimeString("en-MY")}`);
    } catch (x) {
      d || zl(x.message);
    }
  }
  Ul.useEffect(() => {
    _();
  }, []), Ul.useEffect(() => {
    if (G !== "live") return;
    const d = window.setInterval(() => Y(!0), 1e4);
    return () => window.clearInterval(d);
  }, [G]);
  const K = p.zones.find((d) => d.id === Sl), P = p.walls.find((d) => d.id === Sl), o = p.assets.find((d) => d.id === Sl), E = Al.find((d) => String(d.id) === String(w)), j = Ul.useMemo(() => {
    const d = {};
    for (const x of Al) {
      const M = p.zones.find(
        (q) => x.subLocationId && Number(q.subLocationId) === Number(x.subLocationId) || !x.subLocationId && x.locationId && Number(q.locationId) === Number(x.locationId)
      );
      M && (d[M.id] ||= [], d[M.id].push(x));
    }
    return d;
  }, [Al, p.zones]);
  function O(d, x) {
    const M = Pl.current?.getBoundingClientRect();
    return M ? {
      x: (d - M.left) / M.width * p.width,
      y: (x - M.top) / M.height * p.height
    } : { x: 0, y: 0 };
  }
  function Q(d) {
    if (!b.current || G !== "designer") return;
    const x = O(d.clientX, d.clientY), M = b.current;
    M.kind === "zone-move" ? X((q) => ({ ...q, zones: q.zones.map((el) => el.id === M.id ? {
      ...el,
      x: Math.max(0, Math.min(q.width - el.w, x.x - M.dx)),
      y: Math.max(0, Math.min(q.height - el.h, x.y - M.dy))
    } : el) })) : M.kind === "zone-resize" ? X((q) => ({ ...q, zones: q.zones.map((el) => el.id === M.id ? {
      ...el,
      w: Math.max(80, x.x - el.x),
      h: Math.max(60, x.y - el.y)
    } : el) })) : M.kind === "asset-move" ? X((q) => ({ ...q, assets: q.assets.map((el) => el.id === M.id ? { ...el, x: x.x - M.dx, y: x.y - M.dy } : el) })) : (M.kind === "wall-start" || M.kind === "wall-end") && X((q) => ({ ...q, walls: q.walls.map((el) => el.id === M.id ? M.kind === "wall-start" ? { ...el, x1: x.x, y1: x.y } : { ...el, x2: x.x, y2: x.y } : el) }));
  }
  function J() {
    const d = rf("zone"), x = { id: d, name: "NEW ZONE", x: 350, y: 220, w: 210, h: 130, color: Om[p.zones.length % Om.length], locationId: null, subLocationId: null };
    X((M) => ({ ...M, zones: [...M.zones, x] })), bl(d), Zl("select"), zl("Zone added. Drag it or use the blue corner to resize.");
  }
  function ll() {
    const d = rf("wall");
    X((x) => ({ ...x, walls: [...x.walls, { id: d, x1: 370, y1: 290, x2: 570, y2: 290 }] })), bl(d), Zl("select"), zl("Wall added. Drag either endpoint.");
  }
  function Hl(d) {
    const x = rf("asset"), M = { reader: "RFID Reader", door: "Access Door", camera: "Camera / FR" };
    X((q) => ({ ...q, assets: [...q.assets, {
      id: x,
      type: d,
      label: M[d],
      x: 500,
      y: 280,
      laneId: null,
      deviceAssignmentId: null,
      fromZoneId: null,
      toZoneId: null,
      transitionMode: "bidirectional"
    }] })), bl(x), Zl("select"), zl(`${M[d]} added.`);
  }
  function fl(d) {
    X((x) => ({ ...x, zones: x.zones.map((M) => M.id === Sl ? { ...M, ...d } : M) }));
  }
  function qt(d) {
    X((x) => ({ ...x, walls: x.walls.map((M) => M.id === Sl ? { ...M, ...d } : M) }));
  }
  function bt(d) {
    X((x) => ({ ...x, assets: x.assets.map((M) => M.id === Sl ? { ...M, ...d } : M) }));
  }
  function Da(d) {
    if (!d) return R.devices;
    const x = R.lanes.find((q) => Number(q.id) === Number(d));
    if (!x) return [];
    if (x.rfid_reader_ip)
      return R.devices.filter((q) => q.ip_address === x.rfid_reader_ip);
    const M = new Set(
      R.subLocations.filter((q) => Number(q.location_id) === Number(x.location_id)).map((q) => Number(q.id))
    );
    return R.devices.filter((q) => M.has(Number(q.location_id)));
  }
  function Uu(d) {
    const x = d ? Number(d) : null, q = Da(x).some((el) => Number(el.id) === Number(o?.deviceAssignmentId));
    bt({
      laneId: x,
      deviceAssignmentId: q ? o.deviceAssignmentId : null
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
  async function Fa(d = !1) {
    if (!(!hl || !Yl.design)) {
      St(!0), zl("");
      try {
        const x = N.dataset.mapUrlTemplate.replace("__MAP_ID__", String(hl.id)), M = await fetch(x, {
          method: "PUT",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json", Accept: "application/json" },
          body: JSON.stringify({ layout: p, publish: d, name: hl.name, premiseName: hl.premiseName, floorName: hl.floorName })
        }), q = await M.json();
        if (!M.ok) throw new Error(q.message || "Unable to save map.");
        C(q.data.map), X(q.data.map.layout), zl(q.message);
      } catch (x) {
        zl(x.message);
      } finally {
        St(!1);
      }
    }
  }
  return Rl ? /* @__PURE__ */ f.jsxs("div", { className: "emap-state", children: [
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
          hl.premiseName.toUpperCase()
        ] }),
        /* @__PURE__ */ f.jsx("h1", { children: hl.floorName })
      ] }),
      /* @__PURE__ */ f.jsxs("div", { className: "emap-header-actions", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "emap-tabs", children: [
          /* @__PURE__ */ f.jsxs("button", { className: G === "live" ? "active" : "", onClick: () => _l("live"), children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "location_on" }),
            "Live Movement"
          ] }),
          Yl.design && /* @__PURE__ */ f.jsxs("button", { className: G === "designer" ? "active" : "", onClick: () => _l("designer"), children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "draw" }),
            "Map Designer"
          ] })
        ] }),
        G === "live" ? /* @__PURE__ */ f.jsxs("button", { className: "emap-primary green", onClick: () => Y(!1), children: [
          /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "refresh" }),
          "Refresh live"
        ] }) : /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
          /* @__PURE__ */ f.jsx("button", { className: "emap-secondary", disabled: Ht, onClick: () => Fa(!1), children: "Save draft" }),
          Yl.publish && /* @__PURE__ */ f.jsx("button", { className: "emap-primary", disabled: Ht, onClick: () => Fa(!0), children: "Publish" })
        ] })
      ] })
    ] }),
    /* @__PURE__ */ f.jsxs("div", { className: "emap-status", children: [
      /* @__PURE__ */ f.jsx("span", { className: "status-dot" }),
      gt || (G === "live" ? "Live movement refreshes every 10 seconds" : "Edit the floor layout, then save or publish"),
      /* @__PURE__ */ f.jsxs("span", { children: [
        hl.status,
        " · v",
        hl.version
      ] })
    ] }),
    /* @__PURE__ */ f.jsxs("div", { className: `emap-grid ${G}`, children: [
      G === "designer" && /* @__PURE__ */ f.jsxs("aside", { className: "emap-panel emap-tools", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "panel-heading", children: [
          /* @__PURE__ */ f.jsx("h2", { children: "Map tools" }),
          /* @__PURE__ */ f.jsx("span", { children: "DESIGN" })
        ] }),
        /* @__PURE__ */ f.jsxs("button", { className: Bl === "select" ? "active" : "", onClick: () => Zl("select"), children: [
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
        /* @__PURE__ */ f.jsxs("button", { onClick: ll, children: [
          /* @__PURE__ */ f.jsx("i", { children: "╱" }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("b", { children: "Wall" }),
            /* @__PURE__ */ f.jsx("small", { children: "Add a structural line" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "asset-buttons", children: [
          /* @__PURE__ */ f.jsx("b", { children: "Add asset" }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Hl("door"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "door", children: "D" }),
            "Access door"
          ] }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Hl("reader"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "reader", children: "R" }),
            "RFID reader"
          ] }),
          /* @__PURE__ */ f.jsxs("button", { onClick: () => Hl("camera"), children: [
            /* @__PURE__ */ f.jsx("i", { className: "camera", children: "C" }),
            "Camera / FR"
          ] })
        ] })
      ] }),
      /* @__PURE__ */ f.jsxs("section", { className: "emap-panel map-card", children: [
        /* @__PURE__ */ f.jsxs("div", { className: "map-topbar", children: [
          /* @__PURE__ */ f.jsxs("div", { children: [
            /* @__PURE__ */ f.jsx("b", { children: hl.name }),
            /* @__PURE__ */ f.jsx("small", { children: G === "live" ? `${Al.length} visitor${Al.length === 1 ? "" : "s"} currently mapped` : "Drag, resize and link map objects" })
          ] }),
          /* @__PURE__ */ f.jsxs("div", { className: "zoom", children: [
            /* @__PURE__ */ f.jsx("button", { onClick: () => Qt(Math.max(0.7, kl - 0.1)), children: "−" }),
            /* @__PURE__ */ f.jsxs("span", { children: [
              Math.round(kl * 100),
              "%"
            ] }),
            /* @__PURE__ */ f.jsx("button", { onClick: () => Qt(Math.min(1.4, kl + 0.1)), children: "+" })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "emap-canvas", children: [
          /* @__PURE__ */ f.jsxs("svg", { ref: Pl, viewBox: `0 0 ${p.width} ${p.height}`, style: { transform: `scale(${kl})` }, onPointerMove: Q, onPointerUp: () => b.current = null, onPointerLeave: () => b.current = null, children: [
            /* @__PURE__ */ f.jsx("defs", { children: /* @__PURE__ */ f.jsx("pattern", { id: "emap-grid", width: "20", height: "20", patternUnits: "userSpaceOnUse", children: /* @__PURE__ */ f.jsx("path", { d: "M20 0H0V20", fill: "none", stroke: "#e9edf3", strokeWidth: "1" }) }) }),
            /* @__PURE__ */ f.jsx("rect", { width: p.width, height: p.height, fill: "url(#emap-grid)" }),
            p.zones.map((d) => /* @__PURE__ */ f.jsxs("g", { className: G === "designer" ? "movable" : "", onClick: () => G === "designer" && bl(d.id), onPointerDown: (x) => {
              if (G !== "designer") return;
              const M = O(x.clientX, x.clientY);
              b.current = { id: d.id, kind: "zone-move", dx: M.x - d.x, dy: M.y - d.y }, bl(d.id);
            }, children: [
              /* @__PURE__ */ f.jsx("rect", { x: d.x, y: d.y, width: d.w, height: d.h, rx: "4", fill: d.color, fillOpacity: ".78", stroke: Sl === d.id && G === "designer" ? "#137fec" : "#87909d", strokeWidth: Sl === d.id && G === "designer" ? 3 : 2 }),
              /* @__PURE__ */ f.jsx("text", { x: d.x + d.w / 2, y: d.y + 28, textAnchor: "middle", className: "zone-name", children: d.name }),
              G === "designer" && Sl === d.id && /* @__PURE__ */ f.jsxs("g", { transform: `translate(${d.x + d.w} ${d.y + d.h})`, className: "handle", onPointerDown: (x) => {
                x.stopPropagation(), b.current = { id: d.id, kind: "zone-resize" };
              }, children: [
                /* @__PURE__ */ f.jsx("circle", { r: "10" }),
                /* @__PURE__ */ f.jsx("path", { d: "M-4 4L4-4M0 4L4 0" })
              ] })
            ] }, d.id)),
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
              const M = O(x.clientX, x.clientY);
              b.current = { id: d.id, kind: "asset-move", dx: M.x - d.x, dy: M.y - d.y }, bl(d.id);
            }, children: [
              /* @__PURE__ */ f.jsx("rect", { x: "-16", y: "-16", width: "32", height: "32", rx: "8", className: d.type }),
              /* @__PURE__ */ f.jsx("text", { y: "5", textAnchor: "middle", children: d.type === "reader" ? "R" : d.type === "door" ? "D" : "C" })
            ] }, d.id)),
            G === "live" && Object.entries(j).flatMap(([d, x]) => {
              const M = p.zones.find((q) => q.id === d);
              return x.map((q, el) => {
                const Ua = M.x + 45 + el % 3 * 62, qe = M.y + 75 + Math.floor(el / 3) * 70;
                return /* @__PURE__ */ f.jsxs("g", { className: "visitor-marker", transform: `translate(${Ua} ${qe})`, onPointerEnter: () => Ll(String(q.id)), onPointerLeave: () => Ll(null), children: [
                  /* @__PURE__ */ f.jsx("circle", { r: "20" }),
                  /* @__PURE__ */ f.jsx("text", { y: "5", textAnchor: "middle", children: q.initials }),
                  /* @__PURE__ */ f.jsx("rect", { x: "-40", y: "25", width: "80", height: "20", rx: "10" }),
                  /* @__PURE__ */ f.jsx("text", { y: "39", textAnchor: "middle", className: "visitor-label", children: q.name })
                ] }, q.id);
              });
            }),
            G === "live" && E && (() => {
              const d = p.zones.find((Ye) => (j[Ye.id] || []).some((Fl) => String(Fl.id) === String(E.id)));
              if (!d) return null;
              const M = (j[d.id] || []).findIndex((Ye) => String(Ye.id) === String(E.id)), q = d.x + 45 + M % 3 * 62, el = d.y + 75 + Math.floor(M / 3) * 70, Ua = q > p.width - 300 ? q - 265 : q + 32, qe = Math.max(16, Math.min(p.height - 170, el - 60));
              return /* @__PURE__ */ f.jsxs("g", { className: "visitor-popover", transform: `translate(${Ua} ${qe})`, pointerEvents: "none", children: [
                /* @__PURE__ */ f.jsx("rect", { width: "235", height: "150", rx: "10" }),
                /* @__PURE__ */ f.jsx("circle", { cx: "23", cy: "23", r: "12" }),
                /* @__PURE__ */ f.jsx("text", { x: "23", y: "27", textAnchor: "middle", className: "initials", children: E.initials }),
                /* @__PURE__ */ f.jsx("text", { x: "43", y: "20", className: "name", children: E.name }),
                /* @__PURE__ */ f.jsx("text", { x: "43", y: "34", className: "company", children: E.company }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "63", className: "label", children: "TIME IN" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "63", className: "value", children: gf(E.timeIn) }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "85", className: "label", children: "HOST" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "85", className: "value", children: E.host }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "107", className: "label", children: "CURRENT ZONE" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "107", className: "value", children: d.name }),
                /* @__PURE__ */ f.jsx("text", { x: "14", y: "129", className: "label", children: "LAST DETECTED" }),
                /* @__PURE__ */ f.jsx("text", { x: "88", y: "129", className: "value", children: gf(E.lastSeen) }),
                /* @__PURE__ */ f.jsx("circle", { cx: "18", cy: "142", r: "3", className: "live-dot" }),
                /* @__PURE__ */ f.jsx("text", { x: "27", y: "145", className: "live-label", children: "Currently in premise" })
              ] });
            })()
          ] }),
          G === "live" && Al.length === 0 && /* @__PURE__ */ f.jsxs("div", { className: "canvas-empty", children: [
            /* @__PURE__ */ f.jsx("span", { className: "material-symbols-outlined", children: "sensors_off" }),
            /* @__PURE__ */ f.jsx("b", { children: "No active visitor positions yet" }),
            /* @__PURE__ */ f.jsx("small", { children: "Visitors appear after a mapped RFID or QR movement event." })
          ] })
        ] }),
        /* @__PURE__ */ f.jsxs("div", { className: "map-legend", children: [
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "reader", children: "R" }),
            "RFID reader"
          ] }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "door", children: "D" }),
            "Access door"
          ] }),
          /* @__PURE__ */ f.jsxs("span", { children: [
            /* @__PURE__ */ f.jsx("i", { className: "camera", children: "C" }),
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
        !K && !P && !o && /* @__PURE__ */ f.jsxs("div", { className: "empty-inspector", children: [
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
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Location",
            /* @__PURE__ */ f.jsxs("select", { value: K.locationId || "", onChange: (d) => fl({ locationId: d.target.value ? Number(d.target.value) : null, subLocationId: null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              R.locations.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.location_access }, d.id))
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Sub-location / zone",
            /* @__PURE__ */ f.jsxs("select", { value: K.subLocationId || "", onChange: (d) => fl({ subLocationId: d.target.value ? Number(d.target.value) : null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              R.subLocations.filter((d) => !K.locationId || Number(d.location_id) === Number(K.locationId)).map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.name }, d.id))
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
        P && /* @__PURE__ */ f.jsxs(f.Fragment, { children: [
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
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(P.x1), onChange: (d) => qt({ x1: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "Start Y",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(P.y1), onChange: (d) => qt({ y1: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "End X",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(P.x2), onChange: (d) => qt({ x2: Number(d.target.value) }) })
            ] }),
            /* @__PURE__ */ f.jsxs("label", { children: [
              "End Y",
              /* @__PURE__ */ f.jsx("input", { type: "number", value: Math.round(P.y2), onChange: (d) => qt({ y2: Number(d.target.value) }) })
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
            /* @__PURE__ */ f.jsxs("select", { value: o.laneId || "", onChange: (d) => Uu(d.target.value), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              R.lanes.map((d) => /* @__PURE__ */ f.jsx("option", { value: d.id, children: d.lane }, d.id))
            ] })
          ] }),
          /* @__PURE__ */ f.jsxs("label", { children: [
            "Device assignment",
            /* @__PURE__ */ f.jsxs("select", { value: o.deviceAssignmentId || "", onChange: (d) => bt({ deviceAssignmentId: d.target.value ? Number(d.target.value) : null }), children: [
              /* @__PURE__ */ f.jsx("option", { value: "", children: "Not linked" }),
              Da(o.laneId).length === 0 && o.laneId && /* @__PURE__ */ f.jsx("option", { disabled: !0, children: "No devices found in this lane area" }),
              Da(o.laneId).map((d) => /* @__PURE__ */ f.jsxs("option", { value: d.id, children: [
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
            /* @__PURE__ */ f.jsx("i", { style: { background: d.color } }),
            /* @__PURE__ */ f.jsx("span", { children: d.name }),
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
              /* @__PURE__ */ f.jsx("i", { className: d.type, children: d.type === "reader" ? "R" : d.type === "door" ? "D" : "C" }),
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
          Al.length,
          " PEOPLE ON MAP"
        ] })
      ] }),
      /* @__PURE__ */ f.jsx("div", { className: "occupancy-cards", children: p.zones.map((d) => {
        const x = j[d.id]?.length || 0, M = R.subLocations.find((q) => Number(q.id) === Number(d.subLocationId));
        return /* @__PURE__ */ f.jsxs("article", { className: "occupancy-card", style: { borderTopColor: d.color }, children: [
          /* @__PURE__ */ f.jsxs("div", { children: [
            /* @__PURE__ */ f.jsx("span", { className: "occupancy-colour", style: { background: d.color } }),
            /* @__PURE__ */ f.jsx("b", { children: d.name })
          ] }),
          /* @__PURE__ */ f.jsx("strong", { children: x }),
          /* @__PURE__ */ f.jsx("small", { children: x === 1 ? "PERSON" : "PEOPLE" }),
          /* @__PURE__ */ f.jsx("p", { children: M?.name || "Not linked" })
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
        /* @__PURE__ */ f.jsx("span", { children: gf(d.time) })
      ] }, d.id)),
      Cl.length === 0 && /* @__PURE__ */ f.jsx("div", { className: "empty-log", children: "No RFID or QR movement has been recorded yet." })
    ] })
  ] });
}
const Sf = document.getElementById("emap-root");
Sf && i0.createRoot(Sf).render(/* @__PURE__ */ f.jsx(c0, { root: Sf }));
