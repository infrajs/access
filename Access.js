import AccessData from '/-access/'

let Access = {}

Access.admin = function () {
	return AccessData['admin'];
}
Access.debug = function () {
	return AccessData['debug'];
}
Access.adminTime = function () {
	return AccessData['time'];
}
Access.getDebugTime = function () {
	if (Access.debug()) return time();
	else return Access.adminTime();
}

export {Access}