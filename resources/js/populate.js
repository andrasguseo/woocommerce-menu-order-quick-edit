document.addEventListener('DOMContentLoaded', function() {

	// it is a copy of the inline edit function
	let wp_inline_edit_function = inlineEditPost.edit;

	// we overwrite the it with our own
	inlineEditPost.edit = function(post_id) {

		// let's merge arguments of the original function
		wp_inline_edit_function.apply(this, arguments);

		// get the post ID from the argument
		if (typeof(post_id) == 'object') { // if it is object, get the ID number
			post_id = parseInt(this.getId(post_id));
		}

		// add rows to variables
		const edit_row = document.querySelector('#edit-' + post_id);
		const post_row = document.querySelector('#post-' + post_id);

		const menu_order = post_row.querySelector('.column-menu_order').textContent;

		// populate the inputs with column data
		edit_row.querySelector('input[name="menu_order"]').value = menu_order;
	};
});