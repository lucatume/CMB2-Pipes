# CMB2 Pipes

Easy redirection of [Custom Meta Boxes 2](https://github.com/webdevstudios/CMB2) meta box fields content to other post fields.

## Example code
I'd like to show the user a select dropdown that will allow a [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/) relation (calle `related_post`) to be created between the currently edited post and another post of the site, similarly I'd like the post title to be set replacing any space with an `_` char; this is all the code this will take
	
	function snake_case ($value) {
		return str_replace(' ', '_', $value);
	}	
	
	add_action( 'cmb2_init', 'post_meta_boxes' );
	function post_meta_boxes() {
	
		$post_meta_box = new_cmb2_box( array(
			'id'           => 'post-metabox',
			'title'        => __( 'A Meta Box', 'cmb2' ),
			// a custom post type
			'object_types' => array( 'post' )
		) );
	
		$post_meta_box->add_field( array(
			'name' => __( 'Post title', 'cmb2' ),
			'id'   => cmb2_pipe('post_title', array('<', '>' => 'snake_case'), 'post_title'),
			'type' => 'text',
		) );
	
		$post_meta_box->add_field( array(
			'name' => __( 'Related post', 'cmb2' ),
			'id'   => cmb2_p2p_pipe('related_post','<>', 'related_post' ),
			'type' => 'select',
			'options' => get_posts_ids_and_title_list()
		) );
	}
	
## Installation
Download, copy in the plugins folder and activate.

## Usage
The plugin revolves around the concept of "pipes"; very similar to the UNIX definition of it the plugin will take care of "piping" the input or output of a [Custom Meta Boxes 2](https://github.com/webdevstudios/CMB2) genereated meta box field somewhere else.  
Currently the plugin offers the possibility to pipe to:

* post fields - the content of the columns of the `posts` table; repeatable fields not supported
* Posts 2 Posts relations - the relation between a currently edited post and another post

Posts 2 Posts relations between users and posts and users and users are still unpredictable.
To "pipe" the content of a meta box field in a direction the plugin functions should be used as in the example above: the plugin functions will always return the `field_id`, the first argument, to allow proper set up of [Custom Meta Boxes 2](https://github.com/webdevstudios/CMB2) meta boxes to go on.

## Pipe directions
The pipes the plugin will create can go in one of 3 directions:

* `>` - write; the content of the meta field will be written in the `postmeta` table and to the target field, it will be read from the `postmeta` table
* `<` - read; the content of the meta field will not be written to the `postmeta` table or anywhere: it will just read from the target
* `<>` - read and write; the content of the meta field will be written (according to the pipe type) and read (again according to the pipe type) to/from the pipe target, no meta what so ever will be stored in the `postmeta` table.

## Pipe filters
After a value has been read from the pipe target or before it's written to the pipe target filters can be applied.  
This will not always make sense as in the case of [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/) connections but the choice is left to the developer.
When specifying a pipe direction, the `$direction` parameter, an array detailing each pipe direction filters can be passed as the argument

	cmb2_pipe('field_id', array('>' => 'ucwords', '<' => 'filter_function'), 'post_title');
	
The code above means that the `ucwords` function will be applied to the value before writing to the pipe target and the `filter_function` will be applied to the value after it's been read from the pipe target.  
Filter functions can be anything eligible for the `call_user_func` function so closures (PHP >= 5.3) and class/methods are accepted.

### Post Field piping
The function that allows piping to, from and to/from a post fields is 

	cmb2_pipe( $field_id, $direction, $target_post_field );
	
Where the arguments are:

* `$field_id` - a string identifying the field in the meta box
* `$direction` - either a string representing a direction (see above) or an array of direction to filter pairs.
* `$target_post_field` - one of the columns of the `posts` table in the database

Code like the one below will read and write from the `post_title`:

		$post_meta_box->add_field( array(
			'name' => __( 'Post title', 'cmb2' ),
			'id'   => cmb2_pipe('post_title', '<>', 'post_title'),
			'type' => 'text',
		) );

and code like this will instead write to the post title and to the `_entry_title` meta

		$post_meta_box->add_field( array(
			'name' => __( 'Post title', 'cmb2' ),
			'id'   => cmb2_pipe('_entry_title', '>', 'post_title'),
			'type' => 'text',
		) );
		
While a measure or validation/sanitization is applied by the plugin and the WordPress core functions the developer should take the ultimate responsibility about the content that's piped.

### Posts 2 Posts piping
The function that allows piping to, from and to/from one or more Posts 2 posts relations is 

	cmb2_p2p_pipe( $field_id, $direction, $p2p_type, $connection_direction = 'from' )
	
Where the arguments are:

* `$field_id` - a string identifying the field in the meta box
* `$direction` - either a string representing a direction (see above) or an array of direction to filter pairs.
* `$p2p_type` - a string for the Posts 2 Posts connection type, it's the same used in the `p2p_register_connection_type` function ( the `name` parameter)
* `$connection_direction` - optional, either `from` or `to`, defaults to `from`; by default the id of he object that's being edited will be used as the `from` end of the p2p connection that's being created, this parameter allows for that default behaviour to be overwritten.

Fields piping to [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/) connections can be multiple

		$post_meta_box->add_field( array(
			'name' => __( 'Related post', 'cmb2' ),
			'id'   => cmb2_p2p_pipe('related_post','<>', 'related_post' ),
			'type' => 'select',
			'options' => get_posts_ids_and_title_list(),
			'repeatable' => true
		) );
		
a connection for each value will be created, the value is expected to be a post ID. Once again the validation/sanitization part is left to the developer using the plugin.
