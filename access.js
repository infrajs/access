infra.admin = function () {
	return infra.loadJSON('-access/')['admin'];
}
infra.debug = function () {
	return infra.loadJSON('-access/')['debug'];
}
/*infra.test = function () {
	return infra.loadJSON('-access/')['test'];
}*/