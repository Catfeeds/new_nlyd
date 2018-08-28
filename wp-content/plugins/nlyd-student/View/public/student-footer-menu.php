<?php
/**
 * 公用脚
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/17
 * Time: 11:22
 */
?>

<div class="nl-foot-nav">
    <a class="nl-foot-item active" href="<?=home_url();?>">
        <div class="nl-foot-icon">
            <div class="footer-home"></div>
        </div>
        <div class="nl-foot-name">首页</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/account/matchList');?>">
        <div class="nl-foot-icon">
            <div class="footer-train"></div>
        </div>
        <div class="nl-foot-name">训练</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/matchs');?>">
        <div class="nl-foot-icon">
            <div class="footer-match"></div>
        </div>
        <div class="nl-foot-name">比赛</div>
    </a>
    <a class="nl-foot-item">
        <div class="nl-foot-icon">
            <div class="footer-kaoji"></div>
        </div>
        <div class="nl-foot-name">考级</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('account')?>">
        <div class="nl-foot-icon">
            <div class="footer-user"></div>
        </div>
        <div class="nl-foot-name">我的</div>
    </a>
</div>
