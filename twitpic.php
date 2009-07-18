<?php
/*
Plugin Name: TwitPic
Plugin URI: http://www.grobekelle.de/wordpress-plugins
Description: Displays the latest pictures from your Twitpic account in the sidebar of your blog. Get more <a href="http://www.grobekelle.de/wordpress-plugins">Wordpress Plugins</a> by <a href="http://www.grobekelle.de">Grobekelle</a>.
Version: 0.1
Author: grobekelle
Author URI: http://www.grobekelle.de
*/

/**
 * v0.1 07.07.2009 initial release
 */
class TwitPic {
  var $id;
  var $title;
  var $plugin_url;
  var $version;
  var $name;
  var $url;
  var $options;
  var $locale;
  var $cache_file;

  function TwitPic() {
    $this->id         = 'twitpic';
    $this->title      = 'TwitPic';
    $this->version    = '0.1';
    $this->plugin_url = 'http://www.grobekelle.de/wordpress-plugins';
    $this->name       = 'TwitPic v'. $this->version;
    $this->url        = get_bloginfo('wpurl'). '/wp-content/plugins/' . $this->id;

	  $this->locale     = get_locale();
    $this->path       = dirname(__FILE__);
    $this->cache_file = $this->path . '/cache/cache.html';

	  if(empty($this->locale)) {
		  $this->locale = 'en_US';
    }

    load_textdomain($this->id, sprintf('%s/%s.mo', $this->path, $this->locale));

    $this->loadOptions();

    if(!is_admin()) {
      add_filter('wp_head', array(&$this, 'blogHeader'));
    }
    else {
      add_action('admin_menu', array( &$this, 'optionMenu')); 
    }

    add_action('widgets_init', array( &$this, 'initWidget')); 
  }

  function optionMenu() {
    add_options_page($this->title, $this->title, 8, __FILE__, array(&$this, 'optionMenuPage'));
  }

  function optionMenuPage() {
?>
<div class="wrap">
<h2><?=$this->title?></h2>
<div align="center"><p><?=$this->name?> <a href="<?php print( $this->plugin_url ); ?>" target="_blank">Plugin Homepage</a></p></div> 
<?php

  if(isset($_POST[$this->id])) {
    /**
     * nasty checkbox handling
     */
    foreach(array('link_images', 'nofollow', 'show_twitter_link', 'show_twitpic_link', 'target_blank') as $field ) {
      if(!isset($_POST[$this->id][$field])) {
        $_POST[$this->id][$field] = '0';
      }
    }
    
    @unlink($this->cache_file);

    $this->updateOptions($_POST[$this->id]);

    echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved!', $this->id) . '</strong></p></div>'; 
  }
?>
<form method="post" action="options-general.php?page=<?=$this->id?>/<?=$this->id?>.php">

<table class="form-table">
<?php if(!file_exists($this->path.'/cache/') || !is_writeable($this->path.'/cache/')): ?>
<tr valign="top"><th scope="row" colspan="4"><span style="color:red;"><?php _e('Warning! The cachedirectory is missing or not writeable!', $this->id); ?></span><br /><em><?php echo $this->path; ?>/cache</em></th></tr>
<?php endif; ?>

<tr valign="top">
  <th scope="row"><?php _e('Title', $this->id); ?></th>
  <td colspan="3"><input name="<?=$this->id?>[title]" type="text" id="" class="code" value="<?=$this->options['title']?>" /><br /><?php _e('Title is shown above the Widget. If left empty can break your layout in widget mode!', $this->id); ?></td>
</tr>

<tr valign="top">
  <th scope="row"><?php _e('Username', $this->id); ?></th>
  <td colspan="3"><input name="<?=$this->id?>[username]" type="text" id="" class="code" value="<?=$this->options['username']?>" />
  <br /><?php _e('Your Twitter/Twitpic username!', $this->id); ?></td>
</tr>

<tr valign="top">
  <th scope="row"><?php _e('Limit', $this->id); ?></th>
  <td colspan="3"><input name="<?=$this->id?>[limit]" type="text" id="" class="code" value="<?=$this->options['limit']?>" />
  <br /><?php _e('Max. number of images to display!', $this->id); ?></td>
</tr>

<tr valign="top">
  <th scope="row"><?php _e('Widget width', $this->id); ?></th>
  <td colspan="3"><input name="<?=$this->id?>[width]" type="text" id="" class="code" value="<?=$this->options['width']?>" />
  <br /><?php _e('Width of widget wrapper.', $this->id); ?></td>
</tr>

<tr valign="top">
  <th scope="row"><?php _e('Image width', $this->id); ?></th>
  <td colspan="3"><input name="<?=$this->id?>[thumb_width]" type="text" id="" class="code" value="<?=$this->options['thumb_width']?>" />
  <br /><?php _e('Width of thumbnails.', $this->id); ?></td>
</tr>

<tr>
<th scope="row" colspan="4" class="th-full">
<label for="">
<input name="<?=$this->id?>[link_images]" type="checkbox" id="" value="1" <?php echo $this->options['link_images']=='1'?'checked="checked"':''; ?> />
<?php _e('Link images to their Twitpic page?', $this->id); ?></label>
</th>
</tr>

<tr>
<th scope="row" colspan="4" class="th-full">
<label for="">
<input name="<?=$this->id?>[nofollow]" type="checkbox" id="" value="1" <?php echo $this->options['nofollow']=='1'?'checked="checked"':''; ?> />
<?php _e('Set the link to relation nofollow?', $this->id); ?></label>
</th>
</tr>

<tr>
<th scope="row" colspan="4" class="th-full">
<label for="">
<input name="<?=$this->id?>[target_blank]" type="checkbox" id="" value="1" <?php echo $this->options['target_blank']=='1'?'checked="checked"':''; ?> />
<?php _e('Open link in new window?', $this->id); ?></label>
</th>
</tr>

<tr>
<th scope="row" colspan="4" class="th-full">
<label for="">
<input name="<?=$this->id?>[show_twitter_link]" type="checkbox" id="" value="1" <?php echo $this->options['show_twitter_link']=='1'?'checked="checked"':''; ?> />
<?php _e('Show a link to my Twitter profile below the widget?', $this->id); ?></label>
</th>
</tr>

<tr>
<th scope="row" colspan="4" class="th-full">
<label for="">
<input name="<?=$this->id?>[show_twitpic_link]" type="checkbox" id="" value="1" <?php echo $this->options['show_twitpic_link']=='1'?'checked="checked"':''; ?> />
<?php _e('Show a link to my Twitpic profile below the widget?', $this->id); ?></label>
</th>
</tr>


</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('save', $this->id); ?>" class="button" />
</p>
</form>

</div>
<?php
  }

  function updateOptions($options) {

    foreach($this->options as $k => $v) {
      if(array_key_exists( $k, $options)) {
        $this->options[ $k ] = trim($options[ $k ]);
      }
    }

		update_option($this->id, $this->options);
	}
  
  function loadOptions() {
#  delete_option($this->id);
    $this->options = get_option($this->id);

    if(!$this->options) {
      $this->options = array(
        'installed' => time(),
        'username' => '',
        'nofollow' => 1,
        'target_blank' => 1,
        'limit' => 8,
        'thumb_width' => 80,
        'width' => 160,
        'link_images' => 1,
        'show_twitpic_link' => 1,
        'show_twitter_link' => 1,
        'title' => 'TwitPics'
			);

      add_option($this->id, $this->options, $this->name, 'yes');

    }
  }
  
  function httpGet($url) {

    if(!class_exists('Snoopy')) {
      include_once(ABSPATH. WPINC. '/class-snoopy.php');
    }

	  $Snoopy = new Snoopy();

    if(@$Snoopy->fetch($url)) {

      if(!empty( $Snoopy->results)) {
        return $Snoopy->results;
      }
    }

    return false;
  }

  function initWidget() {
    if(function_exists('register_sidebar_widget')) {
      register_sidebar_widget($this->title . ' Widget', array($this, 'showWidget'), null, 'widget_twitpic');
    }
  }

  function showWidget( $args ) {
    extract($args);
    printf( '%s%s%s%s%s%s', $before_widget, $before_title, $this->options['title'], $after_title, $this->getCode(), $after_widget );
  }

  function blogHeader() {
    printf('<meta name="%s" content="%s/%s" />' . "\n", $this->id, $this->id, $this->version);
    printf('<link rel="stylesheet" href="%s/styles/%s.css" type="text/css" media="screen" />'. "\n", $this->url, $this->id);
    printf('<style>#twitpic {width: %dpx imporant!;}</style>', $this->options['width']);
  }

  function getToken($data, $pattern) {
    if(preg_match('|<' . $pattern . '>(.*?)</' . $pattern . '>|s', $data, $matches)) {
      return $matches[1];
    }
    return '';
  }

  function getPictures($user) {
    if(empty($user)) {
      return false;
    }
    /**
     * not the best way, but we can't assume that every webhost simplexml installed
     */
    $data = $this->httpGet('http://twitpic.com/photos/'. $user. '/feed.rss');

    if($data !== false) {

      if(preg_match_all('/<item>(.*?)<\/item>/s', $data, $matches)) {

        $result = array();

        foreach($matches[0] as $match) {
          
          $link = $this->getToken($match, 'link');
                
          $result[] = array(
            'link' => $link,
            'thumb' => str_replace('http://twitpic.com/', 'http://twitpic.com/show/thumb/', $link),
            'date' => $this->formatTime($this->getToken($match, 'pubDate'))
          );
        }

        return array_slice($result, 0, $this->options['limit']);
      }
    }
    return false;
  }
  function formatMessage($s) {
    $rel = intval($this->options['nofollow']) == 1 ? ' rel="nofollow"' : '';
    $target = intval($this->options['target_blank']) == 1 ? ' target="_blank"' : '';
    
    // links
    if(intval($this->options['link_links']) == 1) {
      $s = preg_replace("/\s([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i",sprintf(" <a href=\"$1\" class=\"twitpic-link-link\"%s%s>$1</a>$2", $rel, $target), $s);
      $s = preg_replace("/\s(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i",sprintf(" <a href=\"http://$1\" class=\"twitpic-link-link\"%s%s>$1</a>$2", $rel, $target), $s);
    }
/*
    // email
    $s = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto://$1\" class=\"twitter-link-mail\">$1</a>$2", $s);*/

    // #hashtags - Props to Michael Voigt
    if(intval($this->options['link_hashtags']) == 1) {
      $s = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', sprintf("$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitpic-link-hashtag\"%s%s>#$2</a>$3 ", $rel, $target), $s);
    }
    // @twitter-user
    if(intval($this->options['link_names']) == 1) {
      $s = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', sprintf("$1<a href=\"http://twitter.com/$2\" class=\"twitpic-link-user\"%s%s>@$2</a>$3 ", $rel, $target), $s);
    }
    
    return trim($s);
  }
  
  function formatTime($t) {

    $time = strtotime($t);

    if(abs(time() - $time) < 86400) {
      $time = sprintf(__('%s ago', $this->id), human_time_diff($time));
    }
    else {
      $time = sprintf(__('at %s', $this->id), date(get_option('date_format'), $time));
    }

    return $time;
  }

  function getCode() {

    if(empty($this->options['username'])) {
      return __('Username missing! Please configure the plugin first!', $this->id);
    }
    
    $create = false;

    if(!file_exists($this->cache_file)) {
      $create = true;
    }
    elseif(time() - filemtime($this->cache_file) > 1800) {
      $create = true;
    }
    
    if(!$create) {
      return file_get_contents($this->cache_file);
    }
    
    $pictures = $this->getPictures($this->options['username']);

    if(is_array($pictures)) {

      $data = '';

      foreach($pictures as $picture) {
        $image = '<img src="'. $picture['thumb']. '" border="0" width="'. $this->options['thumb_width']. '" title="'.$picture['date'].'" />';

        if(intval($this->options['link_images']) == 1) {        
          $item = sprintf('<a href="%s"%s%s>%s</a>', $picture['link'], $this->options['target_blank'] == 1 ? ' target="_blank"' : '', $this->options['nofollow'] == 1 ? ' rel="nofollow"' : '', $image);
        }
        else {
          $item = $image;
        }
        
        $data .= $item . "\n";
      }

      $data = '<div id="twitpic">'. $data;

      if(intval($this->options['show_twitter_link'])==1) {
        $data .= '<strong><a class="twitter" href="http://twitter.com/'.$this->options['username'].'" rel="nofollow" target="_blank">'.__('Follow me!', $this->id).'</a></strong>';
      }
      
      if(intval($this->options['show_twitpic_link'])==1) {
        $data .= '<strong><a class="twitpic" href="http://twitpic.com/photos/'.$this->options['username'].'" rel="nofollow" target="_blank">'.__('My Twitpic!', $this->id).'</a></strong>';
      }

      $data .= '<div class="twitpic-footer"><a href="http://www.grobekelle.de/wordpress-plugins" target="_blank" class="snap_noshots">Plugin</a> by <a href="http://www.grobekelle.de" target="_blank" class="snap_noshots">Grobekelle</a></div></div>';

      if(is_writeable($this->path. '/cache')) {
        file_put_contents($this->cache_file, $data);
      }

      return $data;
    }
    
    return '';
  }
}

function twitpic_display() {

  global $TwitPic;

  if($TwitPic) {
    echo $TwitPic->getcode();
  }
}

add_action( 'plugins_loaded', create_function( '$TwitPic_293ka9', 'global $TwitPic; $TwitPic = new TwitPic();' ) );

?>