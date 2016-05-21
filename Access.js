
window.Access = {};

Access.admin = function () {
	return infra.loadJSON('-access/')['admin'];
}
Access.debug = function () {
	return infra.loadJSON('-access/')['debug'];
}

if (!window.infra) window.infra={}; if (!window.infrajs) window.infrajs={};
infra.admin = Access.admin;
infra.debug = Access.debug;