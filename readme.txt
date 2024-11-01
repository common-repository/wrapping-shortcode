=== Wrapping ShortCode ===
Contributors:      Asumaru
Donate link:       https://asumaru.com/plugins/asm-wrapping-shortcode-block/
Tags:              block, shortcode, group, postmeta, commentout, loop, asumaru
Requires at least: 5.9
Tested up to:      6.1
Stable tag:        0.1.0
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

This plugin provides a shortcode block. It is not just a shortcode, it is a shortcode block that wraps other blocks.

== Description ==

Are you using shortcodes?
There is also a standard shortcode block in the default block editor.
But it can't be used without writing code.
Also, you can't enclose other blocks in shortcodes, so you have to write the HTML code.

This plugin provides a shortcode block. It is not just a shortcode, it is a shortcode block that wraps other blocks.

** Easy configuration **

This is available by simply selecting a shortcode that you have enabled in WordPress.
Shortcode arguments are also configurable.
These are configurable in the block editor sidebar.
Also, the selected shortcode name is displayed in the block on the editor.

** Nestable blocks **

This block can place blocks such as "paragraph" and "image".
This blocks can also be nested.
For example, it is possible to repeat headings and paragraphs in combination with shortcodes that can list custom fields.

This block can set a "Comment Label".
Write the specified HTML comment out before and after this block.
Useful for design adjustments.
If you don't set it, it won't be written in the HTML source.

** replacement keyword **

This block has some replacement keywords.
Keywords are used to display shortcode information.
For example, if you enter "%shortcode%" in the Additional CSS class, the selected shortcode name will be displayed in the CSS class.

This block itself does not output HTML tags.
Please use it together with "Group" block.

This plugin includes "commentout" and "meta-loop" as sample shortcodes.

There are many other shortcodes in the world.
Take advantage of useful shortcode assets in this block.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/asm-wrapping-shortcode-block` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Edit a post or page, select Block Editor and add a "Wrapping ShortCode" block.
1. Select the block and set "Shortcode" and "Arguments" from "Settings".
1. Place some blocks inside the block.

== Frequently Asked Questions ==

= What happens if I don't specify a shortcode? =

The blocks placed within the block will be displayed.

= What happens if you don't set the arguments? =

Arguments are optional.
Follow the shortcode you specify.

= What happens if there are no child blocks? =

Child blocks are optional.
nothing happens

= What is the icon? =

It is a Japanese food "Onigiri"(rice ball).

= Why is the icon "Onigiri"? =

Onigiri is often wrapped in seaweed.
The "rice" inside is the child blocks.
The "seaweed" that he wraps around is likened to the shortcode.

= Do you support unkeyed and keyed arguments? =

It is okay to mix unkeyed and keyed arguments.
They are processed separately internally.
It also supports the presence or absence of double quotes.

= Can replacement keywords be used in arguments, comment labels, and additional CSS classes? =

Yes.
However, if you nest this block, the replacement keyword takes precedence over the child blocks.

== Screenshots ==

1. Block icon in the block editor
2. "Wrapping ShortCode" block immediately after placement
3. Place "headings" and "paragraphs" in child blocks
4. Select "Shortcode" and set "Arguments"
5. Describe each content of the child block
6. Add "sample1", "sample2" and "sample3" to custom fields
7. Output result
8. HTML source

== Changelog ==

= 0.1.0 =
* Release

== Upgrade Notice ==

= 0.1.0 =
* Release

== Usage ==

= Child blocks =

Child blocks are optional.
Child blocks can be placed inside the block.
It is also possible to use dynamic blocks.
The "Wrapping ShortCode" itself is also available for child blocks.
It is possible to use replacement keywords within child blocks.
"Wrapping ShortCode" itself has no HTML tags.
Use with "Group" block

= ShortCode =

List valid shortcodes within your WordPress site.
Select the shortcode you want to use.

*In the Pro version (paid), a shortcode filtering function is under development.

= Arguments =

Arguments are optional.
Arguments can be set for the specified shortcode.
It is okay to mix unkeyed and keyed arguments.
It also supports the presence or absence of double quotes.
It is possible to use replacement keywords in arguments.

= Commnet labels =

Commnet labels are optional.
Write the specified HTML comment out before and after this block.
If you don't set it, it won't be written in the HTML source.
It is possible to use replacement keywords in comment labels.

= Additional CSS Classes =

Additional CSS classes are standard items.
Additional CSS classes are optional.
It is possible to use replacement keywords within additional CSS classes.

= Replacement keywords =

Replace any of the following replacement keywords in the block with the appropriate values.
However, if the shortcodes or child blocks is nested, the replacement keyword will take precedence over the shortcodes or child blocks.
* %shortcode%: Selected shortcode name
* %shortcodeAttrs%: Arguments of the set shortcode
* %shortcodeStr%: Generated shortcode string
* %wscClass%: Additional CSS class

== Shortcode: commentout ==

= Overview =

[commentout (arguments) ]comment here[/commentout]

Writes the contents of the shortcode as HTML comments out.

= Arguments =

* trim_br: Remove < br > before and after content. Default "yes"
* do_trim: Remove whitespace before and after content. Default "no"
* do_shortcode: Run a content shortcode. Default "yes"
* esc_html: Escape HTML tags in content. Default "yes"

== Shortcode: meta-loop ==

= Overview =

[meta-loop (arguments) ]template HTML[/meta-loop]

Template and repeat the content of her shortcode by listing the custom field keys for the post.

eg. [meta-loop sample1 sample2 sample3 order=random]<p>#%number%) %key%:%value%</p>[/meta-loop]

= Arguments =

* (meta key): The key of the custom field to output. Multiple allowed. Sequential number if omitted.
* start: Output start position. 0 at the beginning. Default "0"
* end: Output end position. Number of keys if null. Default "null"
* step: position increment/decrement value. Default "1"
* lines: Number of outputs. Default "null". null is output until the end. 0 is hidden.
* order: direction of sorting. default "none"
** none: no sorting.
** asc: Order sort.
** desc: Reverse sort.
** random: Random.
* orderby: what to sort
** counter: output position
** metakey: the key of the custom field
** metavalue: custom field value
* replaceNumber: Replacement keyword for output order. Default "%number%"
* replaceCounter: replacement keyword for output position. Default "%counter%"
* repkaceKey: Replacement keyword for the key of custom field. Default "%key%"
* repkaceValue: Replacement keyword for custom field value. Default "%value%"
* noContent: Alternate content if no template is specified.
* nl2br: Whether to convert BR tags when there is a newline in the custom field value. Default "yes".

= Alternate content =

< div class="no-%number%">%key%:%value%</div >

= replacement keyword =

* %counter%: Repeated number starting from 0
* %number%: Repeated number starting from 1
* %key%: the key of the custom field
* %value%: custom field value

