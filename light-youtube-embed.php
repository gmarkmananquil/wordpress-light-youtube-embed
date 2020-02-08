<?php
/**
 * Plugin Name: Light Youtube Embed
 * Author: Glen Mark Mananquil
 * Author URI: gmarkmananquil.wordpress.com
 * Description: Image replaces the video after load, video plays after image user click the image
 *
 */


if(!defined("ABSPATH")) die("Cannot access directly");

if(defined("LYE_PATH")) die("Duplicate functionality");

define("LYE_PATH", __FILE__);
define("LYE_DIR", dirname(__FILE__));

class lye{

	public function __construct()
	{
		//add_thickbox();
        //Create custom shortcode
		add_shortcode("light_youtube_embed", [$this, "shortcode"]);

		//Place a button in the admin editor.
        add_filter("admin_head", [$this, "add_tinymce_button"]);
		add_action("edit_form_after_editor", [$this, "modal"]);
	}

	public function shortcode($atts)
	{
		$atts = shortcode_atts(
			[
				"url" => "",
				"img" => ""
			], $atts, "light_youtube_embed"
		);

		extract($atts);

		//TODO: filter with regular expression the youtube url instead
		if(empty($url)) return;

		$code = explode("=", $url);

		ob_start();
		?>
		<div class="youtube-player" data-id="<?php echo $code[1]; ?>" data-img="<?php echo $img; ?>"></div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		//Print the script in the footer
        //only show in a page where the shortcode placed
		add_action("wp_print_footer_scripts", [$this, "scripts"], 99);

		return $html;
	}

	public function modal()
	{
        ?>
        <div id="lye-modal" style="display:none;">
            <h3>Light Youtube Embed</h3>
            <i><strong>Light youtube embed is a simple and light way to embed youtube videos</strong></i>


            <p></p>
            <br />
            <label>
                URL : <input type="text" name="url" id="lye-url" class="widefat" placeholder="https://www.youtube.com/watch?v=sd2sed21"/>
                <i>Please enter your youtube url here</i></p>
            </label>
            <label>
                Image Url : <input type="text" name="image-url" id="lye-image" class="widefat" placeholder="http://yourwebsite.com/media/images/my-image.jpeg" />
                <i>Please enter your image url here</i>
            </label>

            <p></p>
            <div style="">
                <a href="#" class="button button-primary" id="lye-done">DONE</a>
            </div>
        </div>
        <a href="#TB_inline?width=600&height=550&inlineId=lye-modal" id="lye-modal-trigger" class="thickbox"></a>
        <?php
	}

	public function add_tinymce_button()
	{
       if(!current_user_can("edit_posts")
            && !current_user_can("edit_pages")) return;

       //check if wysiwyg is enabled
       if('true' == get_user_option('rich_editing')){
           add_filter("mce_buttons", function($buttons){
               array_push($buttons, "lye_mce_button");
               return $buttons;
           });
           add_filter("mce_external_plugins", function($arr){
                $arr["lye_mce_button"] = plugins_url() . "/light-youtube-embed/media/scripts/tnymce_script.js";
                return $arr;
           });
       }

	}

	public function scripts(){
		?>
		<script>
            /* Light YouTube Embeds by @labnol */
            /* Web: http://labnol.org/?p=27941 */
            document.addEventListener("DOMContentLoaded",
                function() {
                    var div, n,
                        v = document.getElementsByClassName("youtube-player");
                    for (n = 0; n < v.length; n++) {
                        div = document.createElement("div");
                        div.setAttribute("data-id", v[n].dataset.id);
                        div.setAttribute("data-img", v[n].dataset.img);
                        div.innerHTML = labnolThumb(v[n].dataset.id, v[n].dataset.img);
                        div.onclick = labnolIframe;
                        v[n].appendChild(div);
                    }
                });

            function labnolThumb(id, img) {
                if(img != "") thumb = '<img src="' + img + '" />';
                play = '<div class="play"></div>';
                return thumb.replace("ID", id) + play;
            }

            function labnolIframe() {
                var iframe = document.createElement("iframe");
                var embed = "https://www.youtube.com/embed/ID?autoplay=1&rel=0";
                iframe.setAttribute("src", embed.replace("ID", this.dataset.id));
                iframe.setAttribute("frameborder", "0");
                iframe.setAttribute("allowfullscreen", "1");
                this.parentNode.replaceChild(iframe, this);
            }
		</script>
		<?php
	}


}


$light_youtube_embed = new lye();