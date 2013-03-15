<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore's tinymce plugin
 * Compatible with Moodle v2.3 only
 *
 * @package    tinymce
 * @subpackage mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
include_once(dirname(__FILE__) . '/../../../../../../../config.php');
defined('MOODLE_INTERNAL') || die('Can\'t find path/to/moodle/config.php.');
require_once($CFG->dirroot . '/local/mediacore/lib.php');

$lti_baseurl = local_mediacore_fetch_lti_baseurl();
$mcore_media = new mediacore_media($lti_baseurl);

//api params
$curr_page = (isset($_GET['page'])) ? (int)$_GET['page'] : 0;
$search = (isset($_GET['search'])) ? (string)$_GET['search'] : '';

//course id
$lti_tools = NULL; $lti_tool_ids = NULL;
$course_id = (isset($_GET['course_id'])) ? (int)$_GET['course_id'] : NULL;
$course_id_val = ($course_id) ? $course_id : '';
if (!empty($course_id)) {
	$lti_tools = local_mediacore_fetch_lti_tools_by_course_id($course_id);
	$lti_tool_ids = local_mediacore_fetch_lti_tool_ids_by_course_id($course_id);
}

//type id
$type_id = (isset($lti_tool_ids[0])) ? (int)$lti_tool_ids[0] : NULL;
if (!empty($_GET['type_id'])) {
	$type_id = (int)$_GET['type_id'];
}
$type_id_val = ($type_id) ? $type_id : '';

//media
$media = $mcore_media->fetch_media($curr_page, $search, $limit=6, $course_id, $type_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html dir="ltr" lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="css/mediacore.css"/>
		<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
		<script type="text/javascript" src="js/mediacore.js"></script>
	</head>
	<body class="" onload="MediaCorePopup.init();">
		<div id="mcore-embed">
			<div class="mcore-lti-select mcore-clearfix">
				<form action="" method="get">
					<label for="type_id">Please select:</label>
					<select name="type_id" onchange="this.form.submit();">
					<?php echo local_mediacore_build_connection_options($lti_tools, $type_id); ?>
					</select>
					<input name="course_id" type="hidden" value="<?php echo $course_id_val; ?>" />
				</form>
			</div>
			<div class="mcore-header">
				<div class="mcore-pagination">
					<a <?php if ($mcore_media->has_previous_page()): ?>
						href="?page=<?php echo $mcore_media->get_previous_page() . "&type_id=$type_id_val&course_id=$course_id_val"; ?>"
						<?php endif; ?>
						class="mcore-btn mcore-prev <?php if (!$mcore_media->has_previous_page()) echo "mcore-disabled"; ?>"
						title="Goto Previous Page"
						onclick="return true;">
						&#8656;
					</a>
					<a <?php if ($mcore_media->has_next_page()): ?>
						href="?page=<?php echo $mcore_media->get_next_page() . "&type_id=$type_id_val&course_id=$course_id_val"; ?>"
						<?php endif; ?>
						class="mcore-btn mcore-next <?php if(!$mcore_media->has_next_page()) echo "mcore-disabled"; ?>"
						title="Goto Next Page"
						onclick="return true">
						&#8658;
					</a>
				</div>
				<div class="mcore-title">MediaCore</div>
			</div>
			<form class="mcore-search" action="" method="get">
				<div>
					<?php if ($mcore_media->get_search_query()): ?>
					<a class="mcore-clear-search" href="?course_id=<?php echo $course_id_val; ?>&type_id=<?php echo $type_id_val; ?>&search=">x</a>
					<?php endif; ?>
					<input name="course_id" type="hidden" value="<?php echo $course_id_val; ?>" />
					<input name="type_id" type="hidden" value="<?php echo $type_id_val; ?>" />
					<input type="text" name="search" id="search" class="mcore-search-field" value="<?php echo $search; ?>" />
				</div>
			</form>
			<div class="mcore-content">
			<?php if ($media) : ?>
				<?php if ($mcore_media->get_rowset_count()== 0): ?>
				<div class="mcore-message">
					<img src="images/zero-state.png" alt="zero state">
					<h2>No media found</h2>
					<p>Either you have no media in your MediaCore library, or your search returned no results.</p>
				</div>
				<?php endif; ?>
				<?php foreach ($media as $m):
					$m = $mcore_media->get_media_row($m);
				?>
				<div class="mcore-media mcore-clearfix mcore-video">
					<div class="mcore-thumbnail">
						<a href="javascript:MediaCorePopup.insert('<?php echo $m->get_url(); ?>', '<?php echo $m->get_escaped_title(); ?>', '<?php echo $type_id_val; ?>');">
							<img src="<?php echo $m->get_thumbs_small_url(); ?>"
							alt="<?php echo $m->get_title(); ?>" />
							<span class="mcore-border"></span>
						</a>
						<div class="mcore-overlay">
							<span class="mcore-length">
								<?php echo $m->get_duration(); ?>
							</span>
							<span class="mcore-icon"></span>
						</div>

					</div>
					<div class="mcore-info">
						<h3>
							<a class="ellipsis" href="javascript:MediaCorePopup.insert('<?php echo $m->get_url(); ?>', '<?php echo $m->get_escaped_title(); ?>', '<?php echo $type_id_val; ?>');">
								<?php echo $m->get_title(); ?>
							</a>
						</h3>
						<span class="mcore-date">
							<?php echo $m->get_publish_on(); ?>
						</span>
					</div>
					<div class="mcore-add">
						<span class="mcore-btn mcore-add-btn">
							<!-- These links are ugly. -->
							<a href="javascript:MediaCorePopup.insert('<?php echo $m->get_url(); ?>', '<?php echo $m->get_escaped_title(); ?>', '<?php echo $type_id_val; ?>');">
								<span class="mcore-icon"></span>Add</a>
						</span>
					</div>
				</div> <!-- /.mcore-media -->
				<?php endforeach ?>
			<?php else: // no media found  ?>
			<div class="mcore-message">
				<h2>No Media</h2>
				<p><?php echo get_string('tinymce_no_media_found', 'local_mediacore'); ?></p>
			<?php endif; ?>
			</div> <!-- /.mcore-content -->
			<div class="mcore-footer">
				<?php echo $mcore_media->get_page_count_str(); ?>
			</div>
		</div> <!-- /#mcore-embed -->
	</body>
</html>
