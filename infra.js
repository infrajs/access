if (window.Template) {
	Template.scope['Access'] = {};
	Template.scope['Access']['adminTime'] = function () {
		return Access.adminTime();
	};
}