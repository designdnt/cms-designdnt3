<?php

class SkeletonController extends BaseController {

    public function run() {
        $article = new ArticleView;
        $id = $article->getStaticId();
        if ($id) {
            $data = Frontend::get();
            $this->modulConfigurator($data);
        } else {
            Dnt::redirect(WWW_PATH . "404");
        }
    }

}
