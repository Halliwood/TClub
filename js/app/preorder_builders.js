var builderData = [{
	value: 97,
	text: '97'
}, {
	value: 100,
	text: '100'
}, {
	value: 138,
	text: '138'
}];

(function($, owner) {
	/**
	 * 所选的构建机
	 **/
	owner.preorder_builders = {};
	owner.preorder_builders.current = {
		value: 0
	};
}(mui, window.orderVo = {}));