<?php

class AutoRedirectModuleController extends Client {

    public function run() {
        $this->init();
        $articleList = new ArticleList();
        $articleView = new ArticleView();
        $rest = new Rest();
        $articleId = $rest->webhook(2);
        $name_url = $articleList->getArticleUrl($articleId, false);
        $url = $this->wwwPath . $this->lang . "/" . $name_url;

        $type = $articleView->getPostParam("type", $articleId);
        //internal redirect
        if (Dnt::in_string("<WWW_PATH>", $name_url)) {
            Dnt::redirect(str_replace("<WWW_PATH>", WWW_PATH, $name_url));
        }

        //external redirect
        if (Dnt::in_string(":\/\/", $name_url)) {
            Dnt::redirect($name_url);
        }

        //sitemap redirect
        foreach (Webhook::getSitemapModules() as $modul) {
            if ($modul == $name_url) {
                Dnt::redirect($url);
            }
        }


        if ($type == "video") {
            Dnt::redirect($articleList->getArticleUrl($articleId, true, $type));
        } else {
            Dnt::redirect($articleList->getArticleUrl($articleId));
        }
    }

}
