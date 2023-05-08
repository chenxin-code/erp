function datetimeFormat(datetime, format) {
    var dt = new Date(datetime);
    var o = {
        'M+': dt.getMonth() + 1,
        'd+': dt.getDate(),
        'h+': dt.getHours(),
        'm+': dt.getMinutes(),
        's+': dt.getSeconds()
    };
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (dt.getFullYear() + '').substr(4 - RegExp.$1.length))
    }
    for (var k in o) {
        if (new RegExp('(' + k + ')').test(format)) {
            format = format.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length)))
        }
    }
    return format;
}
function checkObjectIsEqual(x, y) {
    var in1 = x instanceof Object;
    var in2 = y instanceof Object;
    if (!in1 || !in2) {
        return x === y;
    }
    if (Object.keys(x).length !== Object.keys(y).length) {
        return false;
    }
    for (var p in x) {
        var a = x[p] instanceof Object;
        var b = y[p] instanceof Object;
        if (a && b) {
            return checkObjectIsEqual(x[p], y[p]);
        } else {
            if (x[p] !== y[p]) {
                return false;
            }
        }
    }
    return true;
}