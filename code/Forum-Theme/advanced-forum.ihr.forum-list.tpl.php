<?php
/**
 * @file
 * Default theme implementation to display a list of forums and containers.
 *
 * Available variables:
 * - $forums: An array of forums and containers to display. It is keyed to the
 *   numeric id's of all child forums and containers.
 * - $forum_id: Forum id for the current forum. Parent to all items within
 *   the $forums array.
 *
 * Each $forum in $forums contains:
 * - $forum->is_container: Is TRUE if the forum can contain other forums. Is
 *   FALSE if the forum can contain only topics.
 * - $forum->depth: How deep the forum is in the current hierarchy.
 * - $forum->zebra: 'even' or 'odd' string used for row class.
 * - $forum->name: The name of the forum.
 * - $forum->link: The URL to link to this forum.
 * - $forum->description: The description of this forum.
 * - $forum->new_topics: True if the forum contains unread posts.
 * - $forum->new_url: A URL to the forum's unread posts.
 * - $forum->new_text: Text for the above URL which tells how many new posts.
 * - $forum->old_topics: A count of posts that have already been read.
 * - $forum->total_posts: The total number of posts in the forum.
 * - $forum->last_reply: Text representing the last time a forum was posted or
 *   commented in.
 * - $forum->forum_image: If used, contains an image to display for the forum.
 *
 * @see template_preprocess_forums()
 * @see template_preprocess_forum_list()
 * @see theme_forum_list()
 */
?>

<?php
/*
  The $tables variable holds the individual tables to be shown. A table is
  either created from a root level container or added as needed to hold root
  level forums. The following code will loop through each of the tables.
  In each table, it loops through the items in the table. These items may be
  subcontainers or forums. Subcontainers are printed simply with the name
  spanning the entire table. Forums are printed out in more detail. Subforums
  have already been attached to their parent forums in the preprocessing code
  and will display under their parents.
 */
?>

<div class="panel-group">
<?php foreach ($tables as $table_id => $table): ?>
  <?php $table_info = $table['table_info']; ?>



    <div id="collapse-<?php print $table_info->tid ?>" class="panel-collapse collapse in">

      <table class="table">
        <thead class="forum_head">
        <tr>
          <th>&nbsp;</th>
          <th><?php print $table_info->name ? $table_info->name : t('Forum'); ?></th>
          <th class="text-center"><?php print t('Topics'); ?></th>
          <th class="text-center"><?php print t('Posts'); ?></th>
          <th><?php print t('Last post'); ?></th>
        </tr>
        </thead>

        <tbody id="forum-table-<?php print $table_info->tid; ?>-content" class="forum_content">		
		
        <?php foreach ($table['items'] as $item_id => $item): ?>

          <?php if ($item->is_container): ?>
            <tr id="subcontainer-<?php print $item_id; ?>" class="forum-row <?php print $item->zebra; ?> container-<?php print $item_id; ?>-child">
          <?php else: ?>
            <tr id="forum-<?php print $item_id; ?>" class="forum-row <?php print $item->zebra; ?> container-<?php print $item_id; ?>-child">
          <?php endif; ?>

          <td>
            <span style="font-size: 1.5em; <?php print $item->new_topics ? 'color: black' : 'color: LightGrey' ?>" class="glyphicon glyphicon-file"></span>
          </td>

          <td colspan="<?php print $item->is_container ? 4 : 1 ?>">
            <a href="<?php print $item->link; ?>" class="forum_title"><?php print $item->name; ?></a>

            <?php if (!empty($item->description)): ?>
              <p><?php print $item->description; ?></p>
            <?php endif; ?>

            <?php if (!empty($item->subcontainers)): ?>
              <p>
                <small>
                  <strong><?php print t("Subcontainers") ?>:</strong> <?php print $item->subcontainers; ?>
                </small>
              </p>
            <?php endif; ?>

            <?php if (!empty($item->subforums)): ?>
              <p>
                <small>
                  <strong><?php print t("Subforums") ?>:</strong>  <?php print $item->subforums; ?>
                </small>
              </p>
            <?php endif; ?>
          </td>

          <?php if (!$item->is_container): ?>
            <td>
              <?php print $item->total_topics ?>
            </td>

            <td>
              <?php print $item->total_posts ?>
            </td>

            <td>
              <?php print $item->last_post ?>
            </td>
          <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endforeach; ?>
</div>