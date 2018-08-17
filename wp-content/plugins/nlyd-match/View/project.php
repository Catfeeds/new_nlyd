<div class="wrap">
    <h1 class="wp-heading-inline">项目列表</h1>

    <a href="http://127.0.0.1/nlyd/wp-admin/admin.php?page=match-project&action=edit" class="page-title-action">新建项目</a>
    <hr class="wp-header-end">


    <h2 class="screen-reader-text">过滤页面列表</h2>
    <ul class="subsubsub">
        <li class="all"><a href="edit.php?post_type=page" class="current" aria-current="page">全部<span class="count">（9）</span></a> |</li>
        <li class="publish"><a href="admin.php?page=match-project&project_status=1>已结束<span class="count">（8）</span></a> |</li>
        <li class="draft"><a href="admin.php?page=match-project&project_status=2">正在进行<span class="count">（1）</span></a> |</li>
        <li class="trash"><a href="admin.php?page=match-project&project_status=-1">回收站<span class="count">（1）</span></a></li>
    </ul>
    <form id="posts-filter" method="get">

        <p class="search-box">
            <label class="screen-reader-text" for="post-search-input">搜索页面:</label>
            <input type="search" id="post-search-input" name="s" value="">
            <input type="submit" id="search-submit" class="button" value="搜索页面"></p>

        <input type="hidden" name="post_status" class="post_status_page" value="all">
        <input type="hidden" name="post_type" class="post_type_page" value="page">



        <input type="hidden" id="_wpnonce" name="_wpnonce" value="1115eb2488"><input type="hidden" name="_wp_http_referer" value="/nlyd/wp-admin/edit.php?post_type=page">	<div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                    <option value="-1">批量操作</option>
                    <option value="edit" class="hide-if-no-js">编辑</option>
                    <option value="trash">移至回收站</option>
                </select>
                <input type="submit" id="doaction" class="button action" value="应用">
            </div>
            <div class="alignleft actions">
                <label for="filter-by-date" class="screen-reader-text">按日期筛选</label>
                <select name="m" id="filter-by-date">
                    <option selected="selected" value="0">全部日期</option>
                    <option value="201807">2018年七月</option>
                    <option value="201806">2018年六月</option>
                </select>
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="筛选">		</div>
            <div class="tablenav-pages one-page"><span class="displaying-num">9个项目</span>
                <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text">页，共<span class="total-pages">1</span>页</span></span>
<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
            <br class="clear">
        </div>
        <h2 class="screen-reader-text">页面列表</h2><table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="title" class="manage-column column-title column-primary sortable desc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=title&amp;order=asc"><span>标题</span><span class="sorting-indicator"></span></a></th><th scope="col" id="author" class="manage-column column-author">作者</th><th scope="col" id="comments" class="manage-column column-comments num sortable desc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="评论"><span class="screen-reader-text">评论</span></span></span><span class="sorting-indicator"></span></a></th><th scope="col" id="date" class="manage-column column-date sortable asc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=date&amp;order=desc"><span>日期</span><span class="sorting-indicator"></span></a></th><th scope="col" id="presscore-sidebar" class="manage-column column-presscore-sidebar">Sidebar</th><th scope="col" id="presscore-footer" class="manage-column column-presscore-footer">Footer</th>	</tr>
            </thead>

            <tbody id="the-list">
            <tr id="post-3" class="iedit author-self level-0 post-3 type-page status-draft hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-3">选择Privacy Policy</label>
                    <input id="cb-select-3" type="checkbox" name="post[]" value="3">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“Privacy Policy”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=3&amp;action=edit" aria-label="“Privacy Policy”（编辑）">Privacy Policy</a> — <span class="post-state">草稿</span></strong>

                    <div class="hidden" id="inline_3">
                        <div class="post_title">Privacy Policy</div><div class="post_name">privacy-policy</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">open</div>
                        <div class="_status">draft</div>
                        <div class="jj">30</div>
                        <div class="mm">06</div>
                        <div class="aa">2018</div>
                        <div class="hh">12</div>
                        <div class="mn">49</div>
                        <div class="ss">32</div>
                        <div class="post_password"></div><div class="post_parent">0</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=3&amp;action=edit" aria-label="编辑“Privacy Policy”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“Privacy Policy”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=3&amp;action=trash&amp;_wpnonce=b3d0ee6fea" class="submitdelete" aria-label="移动“Privacy Policy”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/?page_id=3&amp;preview=true" rel="bookmark" aria-label="预览“Privacy Policy”">预览</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">最后修改<br><abbr title="2018/06/30 12:49:32">2018-06-30</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-76" class="iedit author-self level-0 post-76 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-76">选择test</label>
                    <input id="cb-select-76" type="checkbox" name="post[]" value="76">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“test”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=76&amp;action=edit" aria-label="“test”（编辑）">test</a></strong>

                    <div class="hidden" id="inline_76">
                        <div class="post_title">test</div><div class="post_name">76-2</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">06</div>
                        <div class="mm">07</div>
                        <div class="aa">2018</div>
                        <div class="hh">15</div>
                        <div class="mn">29</div>
                        <div class="ss">50</div>
                        <div class="post_password"></div><div class="post_parent">0</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=76&amp;action=edit" aria-label="编辑“test”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“test”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=76&amp;action=trash&amp;_wpnonce=45e3afe722" class="submitdelete" aria-label="移动“test”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/76-2/" rel="bookmark" aria-label="查看“test”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/07/06 15:29:50">2018-07-06</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-57" class="iedit author-self level-0 post-57 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-57">选择学生首页</label>
                    <input id="cb-select-57" type="checkbox" name="post[]" value="57">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“学生首页”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=57&amp;action=edit" aria-label="“学生首页”（编辑）">学生首页</a></strong>

                    <div class="hidden" id="inline_57">
                        <div class="post_title">学生首页</div><div class="post_name">student</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">30</div>
                        <div class="mm">06</div>
                        <div class="aa">2018</div>
                        <div class="hh">14</div>
                        <div class="mn">04</div>
                        <div class="ss">50</div>
                        <div class="post_password"></div><div class="post_parent">0</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=57&amp;action=edit" aria-label="编辑“学生首页”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“学生首页”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=57&amp;action=trash&amp;_wpnonce=4d73b0e0b0" class="submitdelete" aria-label="移动“学生首页”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/" rel="bookmark" aria-label="查看“学生首页”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/06/30 14:04:50">2018-06-30</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-66" class="iedit author-self level-1 post-66 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-66">选择个人中心</label>
                    <input id="cb-select-66" type="checkbox" name="post[]" value="66">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“个人中心”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=66&amp;action=edit" aria-label="“个人中心”（编辑）">— 个人中心</a></strong>

                    <div class="hidden" id="inline_66">
                        <div class="post_title">个人中心</div><div class="post_name">account</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">02</div>
                        <div class="mm">07</div>
                        <div class="aa">2018</div>
                        <div class="hh">14</div>
                        <div class="mn">52</div>
                        <div class="ss">37</div>
                        <div class="post_password"></div><div class="post_parent">57</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=66&amp;action=edit" aria-label="编辑“个人中心”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“个人中心”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=66&amp;action=trash&amp;_wpnonce=73389b2cf2" class="submitdelete" aria-label="移动“个人中心”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/account/" rel="bookmark" aria-label="查看“个人中心”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/07/02 14:52:37">2018-07-02</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-61" class="iedit author-self level-1 post-61 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-61">选择学生注册</label>
                    <input id="cb-select-61" type="checkbox" name="post[]" value="61">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“学生注册”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=61&amp;action=edit" aria-label="“学生注册”（编辑）">— 学生注册</a></strong>

                    <div class="hidden" id="inline_61">
                        <div class="post_title">学生注册</div><div class="post_name">register</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">30</div>
                        <div class="mm">06</div>
                        <div class="aa">2018</div>
                        <div class="hh">14</div>
                        <div class="mn">06</div>
                        <div class="ss">21</div>
                        <div class="post_password"></div><div class="post_parent">57</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=61&amp;action=edit" aria-label="编辑“学生注册”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“学生注册”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=61&amp;action=trash&amp;_wpnonce=da7fdd8fbb" class="submitdelete" aria-label="移动“学生注册”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/register/" rel="bookmark" aria-label="查看“学生注册”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/06/30 14:06:21">2018-06-30</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-59" class="iedit author-self level-1 post-59 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-59">选择学生登录</label>
                    <input id="cb-select-59" type="checkbox" name="post[]" value="59">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“学生登录”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=59&amp;action=edit" aria-label="“学生登录”（编辑）">— 学生登录</a></strong>

                    <div class="hidden" id="inline_59">
                        <div class="post_title">学生登录</div><div class="post_name">login</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">30</div>
                        <div class="mm">06</div>
                        <div class="aa">2018</div>
                        <div class="hh">14</div>
                        <div class="mn">05</div>
                        <div class="ss">56</div>
                        <div class="post_password"></div><div class="post_parent">57</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=59&amp;action=edit" aria-label="编辑“学生登录”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“学生登录”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=59&amp;action=trash&amp;_wpnonce=d8260d3ce3" class="submitdelete" aria-label="移动“学生登录”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/login/" rel="bookmark" aria-label="查看“学生登录”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/06/30 14:05:56">2018-06-30</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-64" class="iedit author-self level-1 post-64 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-64">选择密码重置</label>
                    <input id="cb-select-64" type="checkbox" name="post[]" value="64">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“密码重置”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=64&amp;action=edit" aria-label="“密码重置”（编辑）">— 密码重置</a></strong>

                    <div class="hidden" id="inline_64">
                        <div class="post_title">密码重置</div><div class="post_name">reset</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">02</div>
                        <div class="mm">07</div>
                        <div class="aa">2018</div>
                        <div class="hh">13</div>
                        <div class="mn">43</div>
                        <div class="ss">45</div>
                        <div class="post_password"></div><div class="post_parent">57</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=64&amp;action=edit" aria-label="编辑“密码重置”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“密码重置”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=64&amp;action=trash&amp;_wpnonce=506f14e9de" class="submitdelete" aria-label="移动“密码重置”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/reset/" rel="bookmark" aria-label="查看“密码重置”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/07/02 13:43:45">2018-07-02</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-69" class="iedit author-self level-1 post-69 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-69">选择用户协议</label>
                    <input id="cb-select-69" type="checkbox" name="post[]" value="69">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“用户协议”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=69&amp;action=edit" aria-label="“用户协议”（编辑）">— 用户协议</a></strong>

                    <div class="hidden" id="inline_69">
                        <div class="post_title">用户协议</div><div class="post_name">agreement</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">closed</div>
                        <div class="_status">publish</div>
                        <div class="jj">04</div>
                        <div class="mm">07</div>
                        <div class="aa">2018</div>
                        <div class="hh">13</div>
                        <div class="mn">38</div>
                        <div class="ss">00</div>
                        <div class="post_password"></div><div class="post_parent">57</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=69&amp;action=edit" aria-label="编辑“用户协议”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“用户协议”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=69&amp;action=trash&amp;_wpnonce=154d069ccb" class="submitdelete" aria-label="移动“用户协议”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/student/agreement/" rel="bookmark" aria-label="查看“用户协议”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/07/04 13:38:00">2018-07-04</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            <tr id="post-2" class="iedit author-self level-0 post-2 type-page status-publish hentry description-off">
                <th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-2">选择示例页面</label>
                    <input id="cb-select-2" type="checkbox" name="post[]" value="2">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text">“示例页面”已被锁定</span>
                    </div>
                </th><td class="title column-title has-row-actions column-primary page-title" data-colname="标题"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <strong><a class="row-title" href="http://127.0.0.1/nlyd/wp-admin/post.php?post=2&amp;action=edit" aria-label="“示例页面”（编辑）">示例页面</a></strong>

                    <div class="hidden" id="inline_2">
                        <div class="post_title">示例页面</div><div class="post_name">sample-page</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">closed</div>
                        <div class="ping_status">open</div>
                        <div class="_status">publish</div>
                        <div class="jj">30</div>
                        <div class="mm">06</div>
                        <div class="aa">2018</div>
                        <div class="hh">12</div>
                        <div class="mn">49</div>
                        <div class="ss">32</div>
                        <div class="post_password"></div><div class="post_parent">0</div><div class="page_template">default</div><div class="menu_order">0</div></div><div class="row-actions"><span class="edit"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=2&amp;action=edit" aria-label="编辑“示例页面”">编辑</a> | </span><span class="inline hide-if-no-js"><a href="#" class="editinline" aria-label="快速编辑“示例页面”">快速编辑</a> | </span><span class="trash"><a href="http://127.0.0.1/nlyd/wp-admin/post.php?post=2&amp;action=trash&amp;_wpnonce=dbff964c4f" class="submitdelete" aria-label="移动“示例页面”到垃圾箱">移至回收站</a> | </span><span class="view"><a href="http://127.0.0.1/nlyd/sample-page/" rel="bookmark" aria-label="查看“示例页面”">查看</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td><td class="author column-author" data-colname="作者"><a href="edit.php?post_type=page&amp;author=1">cd_leo</a></td><td class="comments column-comments" data-colname="评论">		<div class="post-com-count-wrapper">
                        <span aria-hidden="true">—</span><span class="screen-reader-text">无评论</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">无评论</span></span>		</div>
                </td><td class="date column-date" data-colname="日期">已发布<br><abbr title="2018/06/30 12:49:32">2018-06-30</abbr></td><td class="presscore-sidebar column-presscore-sidebar" data-colname="Sidebar">Default Sidebar</td><td class="presscore-footer column-presscore-footer" data-colname="Footer">Default Footer</td>		</tr>
            </tbody>

            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"></td><th scope="col" class="manage-column column-title column-primary sortable desc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=title&amp;order=asc"><span>标题</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-author">作者</th><th scope="col" class="manage-column column-comments num sortable desc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="评论"><span class="screen-reader-text">评论</span></span></span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-date sortable asc"><a href="http://127.0.0.1/nlyd/wp-admin/edit.php?post_type=page&amp;orderby=date&amp;order=desc"><span>日期</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-presscore-sidebar">Sidebar</th><th scope="col" class="manage-column column-presscore-footer">Footer</th>	</tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="action2" id="bulk-action-selector-bottom">
                    <option value="-1">批量操作</option>
                    <option value="edit" class="hide-if-no-js">编辑</option>
                    <option value="trash">移至回收站</option>
                </select>
                <input type="submit" id="doaction2" class="button action" value="应用">
            </div>
            <div class="alignleft actions">
            </div>
            <div class="tablenav-pages one-page"><span class="displaying-num">9个项目</span>
                <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第1页，共<span class="total-pages">1</span>页</span></span>
<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
            <br class="clear">
        </div>

    </form>


    <form method="get"><table style="display: none"><tbody id="inlineedit">

            <tr id="inline-edit" class="inline-edit-row inline-edit-row-page quick-edit-row quick-edit-row-page inline-edit-page" style="display: none"><td colspan="7" class="colspanchange">

                    <fieldset class="inline-edit-col-left">
                        <legend class="inline-edit-legend">快速编辑</legend>
                        <div class="inline-edit-col">

                            <label>
                                <span class="title">标题</span>
                                <span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value=""></span>
                            </label>

                            <label>
                                <span class="title">别名</span>
                                <span class="input-text-wrap"><input type="text" name="post_name" value=""></span>
                            </label>


                            <fieldset class="inline-edit-date">
                                <legend><span class="title">日期</span></legend>
                                <div class="timestamp-wrap"><label><span class="screen-reader-text">年</span><input type="text" name="aa" value="2018" size="4" maxlength="4" autocomplete="off"></label>-<label><span class="screen-reader-text">月</span><select name="mm">
                                            <option value="01" data-text="1月">1月</option>
                                            <option value="02" data-text="2月">2月</option>
                                            <option value="03" data-text="3月">3月</option>
                                            <option value="04" data-text="4月">4月</option>
                                            <option value="05" data-text="5月">5月</option>
                                            <option value="06" data-text="6月" selected="selected">6月</option>
                                            <option value="07" data-text="7月">7月</option>
                                            <option value="08" data-text="8月">8月</option>
                                            <option value="09" data-text="9月">9月</option>
                                            <option value="10" data-text="10月">10月</option>
                                            <option value="11" data-text="11月">11月</option>
                                            <option value="12" data-text="12月">12月</option>
                                        </select></label>-<label><span class="screen-reader-text">日</span><input type="text" name="jj" value="30" size="2" maxlength="2" autocomplete="off"></label> @ <label><span class="screen-reader-text">时</span><input type="text" name="hh" value="12" size="2" maxlength="2" autocomplete="off"></label>:<label><span class="screen-reader-text">分</span><input type="text" name="mn" value="49" size="2" maxlength="2" autocomplete="off"></label></div><input type="hidden" id="ss" name="ss" value="32">			</fieldset>
                            <br class="clear">

                            <label class="inline-edit-author"><span class="title">作者</span><select name="post_author" class="authors">
                                    <option value="1">cd_leo（cd_leo）</option>
                                </select></label>
                            <div class="inline-edit-group wp-clearfix">
                                <label class="alignleft">
                                    <span class="title">密码</span>
                                    <span class="input-text-wrap"><input type="text" name="post_password" class="inline-edit-password-input" value=""></span>
                                </label>

                                <em class="alignleft inline-edit-or">
                                    –或–				</em>
                                <label class="alignleft inline-edit-private">
                                    <input type="checkbox" name="keep_private" value="private">
                                    <span class="checkbox-title">私密</span>
                                </label>
                            </div>


                        </div></fieldset>


                    <fieldset class="inline-edit-col-right"><div class="inline-edit-col">

                            <label>
                                <span class="title">父级</span>
                                <select name="post_parent" id="post_parent">
                                    <option value="0">主页面（无父级）</option>
                                    <option class="level-0" value="76">test</option>
                                    <option class="level-0" value="57">学生首页</option>
                                    <option class="level-1" value="66">&nbsp;&nbsp;&nbsp;个人中心</option>
                                    <option class="level-1" value="61">&nbsp;&nbsp;&nbsp;学生注册</option>
                                    <option class="level-1" value="59">&nbsp;&nbsp;&nbsp;学生登录</option>
                                    <option class="level-1" value="64">&nbsp;&nbsp;&nbsp;密码重置</option>
                                    <option class="level-1" value="69">&nbsp;&nbsp;&nbsp;用户协议</option>
                                    <option class="level-0" value="2">示例页面</option>
                                </select>
                            </label>


                            <label>
                                <span class="title">排序</span>
                                <span class="input-text-wrap"><input type="text" name="menu_order" class="inline-edit-menu-order-input" value="0"></span>
                            </label>


                            <label>
                                <span class="title">模板</span>
                                <select name="page_template">
                                    <option value="default">默认模板</option>

                                    <option value="template-albums-jgrid.php">Albums - justified grid</option>
                                    <option value="template-albums.php">Albums - masonry &amp; grid</option>
                                    <option value="template-blog-list.php">Blog - list</option>
                                    <option value="template-blog-masonry.php">Blog - masonry &amp; grid</option>
                                    <option value="template-media-jgrid.php">Gallery - justified grid</option>
                                    <option value="template-media.php">Gallery - masonry &amp; grid</option>
                                    <option value="template-microsite.php">Microsite</option>
                                    <option value="template-portfolio-jgrid.php">Portfolio - justified grid</option>
                                    <option value="template-portfolio-list.php">Portfolio - list</option>
                                    <option value="template-portfolio-masonry.php">Portfolio - masonry &amp; grid</option>
                                    <option value="template-team.php">Team</option>
                                    <option value="template-testimonials.php">Testimonials</option>			</select>
                            </label>



                            <div class="inline-edit-group wp-clearfix">
                                <label class="alignleft">
                                    <input type="checkbox" name="comment_status" value="open">
                                    <span class="checkbox-title">允许评论</span>
                                </label>
                            </div>


                            <div class="inline-edit-group wp-clearfix">
                                <label class="inline-edit-status alignleft">
                                    <span class="title">状态</span>
                                    <select name="_status">
                                        <option value="publish">已发布</option>
                                        <option value="future">定时</option>
                                        <option value="pending">等待复审</option>
                                        <option value="draft">草稿</option>
                                    </select>
                                </label>


                            </div>


                        </div></fieldset>

                    <div class="submit inline-edit-save">
                        <button type="button" class="button cancel alignleft">取消</button>
                        <input type="hidden" id="_inline_edit" name="_inline_edit" value="348587d805">				<button type="button" class="button button-primary save alignright">更新</button>
                        <span class="spinner"></span>
                        <input type="hidden" name="post_view" value="list">
                        <input type="hidden" name="screen" value="edit-page">
                        <br class="clear">
                        <div class="notice notice-error notice-alt inline hidden">
                            <p class="error"></p>
                        </div>
                    </div>
                </td></tr>

            <tr id="bulk-edit" class="inline-edit-row inline-edit-row-page bulk-edit-row bulk-edit-row-page bulk-edit-page" style="display: none"><td colspan="7" class="colspanchange">

                    <fieldset class="inline-edit-col-left">
                        <legend class="inline-edit-legend">批量编辑</legend>
                        <div class="inline-edit-col">
                            <div id="bulk-title-div">
                                <div id="bulk-titles"></div>
                            </div>




                        </div></fieldset>


                    <fieldset class="inline-edit-col-right"><div class="inline-edit-col">

                            <label class="inline-edit-author"><span class="title">作者</span><select name="post_author" class="authors">
                                    <option value="-1">—无更改—</option>
                                    <option value="1">cd_leo（cd_leo）</option>
                                </select></label>			<label>
                                <span class="title">父级</span>
                                <select name="post_parent" id="post_parent">
                                    <option value="-1">—无更改—</option>
                                    <option value="0">主页面（无父级）</option>
                                    <option class="level-0" value="76">test</option>
                                    <option class="level-0" value="57">学生首页</option>
                                    <option class="level-1" value="66">&nbsp;&nbsp;&nbsp;个人中心</option>
                                    <option class="level-1" value="61">&nbsp;&nbsp;&nbsp;学生注册</option>
                                    <option class="level-1" value="59">&nbsp;&nbsp;&nbsp;学生登录</option>
                                    <option class="level-1" value="64">&nbsp;&nbsp;&nbsp;密码重置</option>
                                    <option class="level-1" value="69">&nbsp;&nbsp;&nbsp;用户协议</option>
                                    <option class="level-0" value="2">示例页面</option>
                                </select>
                            </label>


                            <label>
                                <span class="title">模板</span>
                                <select name="page_template">
                                    <option value="-1">—无更改—</option>
                                    <option value="default">默认模板</option>

                                    <option value="template-albums-jgrid.php">Albums - justified grid</option>
                                    <option value="template-albums.php">Albums - masonry &amp; grid</option>
                                    <option value="template-blog-list.php">Blog - list</option>
                                    <option value="template-blog-masonry.php">Blog - masonry &amp; grid</option>
                                    <option value="template-media-jgrid.php">Gallery - justified grid</option>
                                    <option value="template-media.php">Gallery - masonry &amp; grid</option>
                                    <option value="template-microsite.php">Microsite</option>
                                    <option value="template-portfolio-jgrid.php">Portfolio - justified grid</option>
                                    <option value="template-portfolio-list.php">Portfolio - list</option>
                                    <option value="template-portfolio-masonry.php">Portfolio - masonry &amp; grid</option>
                                    <option value="template-team.php">Team</option>
                                    <option value="template-testimonials.php">Testimonials</option>			</select>
                            </label>



                            <div class="inline-edit-group wp-clearfix">
                                <label class="alignleft">
                                    <span class="title">评论</span>
                                    <select name="comment_status">
                                        <option value="">—无更改—</option>
                                        <option value="open">允许</option>
                                        <option value="closed">不允许</option>
                                    </select>
                                </label>
                            </div>


                            <div class="inline-edit-group wp-clearfix">
                                <label class="inline-edit-status alignleft">
                                    <span class="title">状态</span>
                                    <select name="_status">
                                        <option value="-1">—无更改—</option>
                                        <option value="publish">已发布</option>

                                        <option value="private">私密</option>
                                        <option value="pending">等待复审</option>
                                        <option value="draft">草稿</option>
                                    </select>
                                </label>


                            </div>


                        </div></fieldset>

                    <div class="inline-edit-col-right" style="display: inline-block; float: left;">
                        <fieldset>
                            <div class="inline-edit-col">

                                <div class="inline-edit-group">
                                    <label class="alignleft">
                                        <span class="title">Sidebar option</span>
                                        <select name="_dt_bulk_edit_sidebar_options">
                                            <option value="-1">— No Change —</option>
                                            <option value="left">Left</option>
                                            <option value="right">Right</option>
                                            <option value="disabled">Disabled</option>
                                        </select>
                                    </label>

                                    <label class="alignright">
                                        <span class="title">Widgetized footer</span>
                                        <select name="_dt_bulk_edit_show_footer">
                                            <option value="-1">— No Change —</option>
                                            <option value="0">Hide</option>
                                            <option value="1">Show</option>
                                        </select>
                                    </label>
                                </div>


                                <div class="inline-edit-group">
                                    <label class="alignleft">
                                        <span class="title">Sidebar</span>
                                        <select name="_dt_bulk_edit_sidebar">
                                            <option value="-1">— No Change —</option>
                                            <option value="sidebar_1">Default Sidebar</option>
                                            <option value="sidebar_2">Default Footer</option>
                                        </select>
                                    </label>

                                    <label class="alignright">
                                        <span class="title">Footer</span>
                                        <select name="_dt_bulk_edit_footer">
                                            <option value="-1">— No Change —</option>
                                            <option value="sidebar_1">Default Sidebar</option>
                                            <option value="sidebar_2">Default Footer</option>
                                        </select>
                                    </label>
                                </div>


                            </div>
                        </fieldset>
                    </div>
                    <div class="submit inline-edit-save">
                        <button type="button" class="button cancel alignleft">取消</button>
                        <input type="submit" name="bulk_edit" id="bulk_edit" class="button button-primary alignright" value="更新">			<input type="hidden" name="post_view" value="list">
                        <input type="hidden" name="screen" value="edit-page">
                        <br class="clear">
                        <div class="notice notice-error notice-alt inline hidden">
                            <p class="error"></p>
                        </div>
                    </div>
                </td></tr>
            </tbody></table></form>

    <div id="ajax-response"></div>
    <br class="clear">
</div>