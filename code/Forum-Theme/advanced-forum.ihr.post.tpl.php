<?php

/**
 * @file
 *
 * Theme implementation: Template for each forum post whether node or comment.
 *
 * All variables available in node.tpl.php and comment.tpl.php for your theme
 * are available here. In addition, Advanced Forum makes available the following
 * variables:
 *
 * - $top_post: TRUE if we are formatting the main post (ie, not a comment)
 * - $reply_link: Text link / button to reply to topic.
 * - $total_posts: Number of posts in topic (not counting first post).
 * - $new_posts: Number of new posts in topic, and link to first new.
 * - $links_array: Unformatted array of links.
 * - $account: User object of the post author.
 * - $name: User name of post author.
 * - $author_pane: Entire contents of the Author Pane template.
 */

?>

<?php if ($top_post): ?>
  <?php //print $topic_header ?>
<?php endif; ?>

<div class="col-xs-12 post-container">
	<?php if (!empty($title)): ?>
		<h4 class="media-heading"><?php print $title ?></h4>
	<?php endif; ?>
	
	<span class="pull-right"><?php print $permalink; ?></span>
    <span class="text-muted"><?php if (!empty($author_pane)): ?>
			von <?php print (isset($name) ? $name : $author); ?>
    <?php endif; ?> &raquo; <?php print $date ?>
	</span>
	<hr>
	
	<?php
	  // We hide the comments and links now so that we can render them later.
	  hide($content['taxonomy_forums']);
	  hide($content['comments']);
	  hide($content['links']);
	  if (!$top_post) hide($content['body']);
	  print render($content);
	?>
	
	<?php if (!empty($post_edited)): ?>
	  <div class="post-edited"><?php print $post_edited ?></div>
	<?php endif; ?>		
	
	<br />
	<hr>

	<?php if (!empty($signature)): ?>
	  <div class="author-signature"><?php print $signature ?></div>
	<?php endif; ?>		
	
	<div class="pull-right">
		<?php print render($content['links']); ?>
	</div>
</div>

<?php print render($content['comments']); ?>
