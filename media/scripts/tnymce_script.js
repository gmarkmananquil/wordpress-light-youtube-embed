(function(){

    tinyMCE.PluginManager.add("lye_mce_button", function(editor, url){
        console.log(url);
        console.log(editor);

        editor.addButton("lye_mce_button", {
            text: "Light Youtube Embed",
            icon: false,
            onclick: function(){
                //alert("inserting...");
                jQuery("#lye-modal-trigger").trigger("click");


                jQuery("#lye-done").one("click", function(){
                    var url = $("#lye-url").val();
                    var image = $("#lye-image").val();
                    editor.insertContent('[light_youtube_embed url="' + url +'" img="' + image + '"]');
                    jQuery("#TB_window .tb-close-icon").trigger("click");
                });

                //
            }
        });
    });

})();