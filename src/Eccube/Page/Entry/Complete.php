<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Page\Entry;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CartSession;
use Eccube\Framework\Response;

/**
 * 会員登録(完了) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Complete extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // カートが空かどうかを確認する。
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        $arrCartKeys = $objCartSess->getKeys();
        $this->tpl_cart_empty = true;
        foreach ($arrCartKeys as $cart_key) {
            if (count($objCartSess->getCartList($cart_key)) > 0) {
                $this->tpl_cart_empty = false;
                break;
            }
        }

        // メインテンプレートを設定
        if (CUSTOMER_CONFIRM_MAIL == true) {
            // 仮会員登録完了
            $this->tpl_mainpage     = 'entry/complete.tpl';
        } else {
            // 本会員登録完了
            Application::alias('eccube.response')->sendRedirectFromUrlPath('regist/complete.php');
        }

    }
}
