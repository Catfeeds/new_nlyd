
<!-- 登陆 -->
<div class="wrapper_content">
    <p class="titleLanguage">
        <span>切换语言</span>
        <span class="nl-light-blue nl-ios-click">中文</span>
    </p>
    <div class="login-box-top">
        <div class="box-logo ">
            <img src="<?=student_css_url.'image/nlyd-big.png'?>" class="logoImg">
        </div>
        <div class="box-logo-name">
            <img src="<?=student_css_url.'image/InternationalIntellectualSports.png'?>" class="logoImg">
        </div>
    </div>
    <div class="layui-tab layui-tab-brief" lay-filter="tabs">
        <ul style="margin-left: 0" class="layui-tab-title  ">
            <li class="layui-this">
                <i class="iconfont iconLock display-hide">&#xe60a;</i>
                <i class="iconfont iconPhone">&#xe61c;</i>&nbsp;&nbsp;&nbsp;<span class="formName">手机快速登陆</span>
            </li>
            <li>
                <i class="iconfont icon-zhuce">&#xe642;</i>&nbsp;&nbsp;&nbsp;<span >注册</span>
            </li>
            <div class="nl-transform">
                <i class="iconfont iconLock display-hide">&#xe60a;</i>
                <i class="iconfont iconPhone">&#xe61c;</i>&nbsp;&nbsp;&nbsp;<span class="formName">手机快速登陆</span>
            </div>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show"> 
                <!-- 手机号码登陆 -->
                <div class="tabs-wraps a1">
                    <form class="layui-form" action="" id='loginFormFast' lay-filter='loginFormFast'>
                        <!-- 使用手机验证码快速登录 -->
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="student_login">
                                <input type="hidden" name="login_type" value="mobile">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_login_code_nonce');?>">
                                <input type="tel" name="user_login" lay-verify="phone" autocomplete="off" placeholder="手机号" class="layui-input ">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="tel" name="password" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input ">
                                <button class="getCodeBtn nl-dark-blue getCode" data-sendCodeCase="19">获取验证码</button>
                            </div>
                        </div>
                        <p class="row-margin">
                            <span  data-show="a3" class="login-by-code nl-dark-blue display-block login-by">使用帐号密码登录</span>
                        </p>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                            <button class="layui-btn submitBtn  nl-bg-blue" id="loginFormFastBtn" lay-filter="loginFormFastBtn" lay-submit="">登 陆</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tabs-wraps display-hide a3">
                    <!-- 使用账号密码 -->
                    <form class="layui-form" action="" id='loginFormPsw' lay-filter='loginFormPsw'>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="student_login">
                                <input type="hidden" name="login_type" value="pass">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_login_code_nonce')?>">
                                <input type="text" name="user_login" lay-verify="phoneOrEmail" autocomplete="off" placeholder="手机号/邮箱" class="layui-input ">
                            </div>
                        </div>
                        <div class="layui-form-item" >
                            <div class="layui-input-inline">
                                <input type="password" name="password" lay-verify="required" placeholder="输入密码" autocomplete="off" class="layui-input ">
                            </div>
                        </div>
                        <p class="row-margin">
                            <span data-show="a1"  class="login-by-psw  nl-dark-blue login-by">使用手机验证码快速登录</span>
                            <span data-show="a2"  class="login-by-reset nl-dark-blue login-by pull-right">忘记密码</span>
                        </p>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <button class="layui-btn submitBtn  nl-bg-blue" id="c" lay-filter="loginFormPswBtn" lay-submit="">登 陆</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tabs-wraps display-hide a2">
                    <!-- 忘记密码 -->
                    <form class="layui-form" action="" id='loginFormForget' lay-filter='loginFormForget'>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="student_reset">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_reset_code_nonce');?>">
                                <input type="text" name="user_login" lay-verify="phoneOrEmail" autocomplete="off" placeholder="手机号/邮箱" class="layui-input ">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="tel" name="verify_code" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input ">
                                <button class="getCodeBtn nl-dark-blue getCode" data-sendCodeCase="16" >获取验证码</button>
                            </div>
                        </div>
                        <div class="layui-form-item" >
                            <div class="layui-input-inline">
                                <input type="password" name="password" lay-verify="password" placeholder="输入新密码" autocomplete="off" class="layui-input ">
                            </div>
                        </div>
                        <div class="layui-form-item" >
                            <div class="layui-input-inline">
                                <input type="password" name="confirm_password" lay-verify="required" placeholder="输入新密码" autocomplete="off" class="layui-input ">
                            </div>
                        </div>
                        <p class="row-margin">
                            <span data-show="a1"  class="login-by-psw  nl-dark-blue login-by">返回登陆</span>
                        </p>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <button class="layui-btn submitBtn  nl-bg-blue" id="loginFormForgetBtn" lay-filter="loginFormForgetBtn" lay-submit="">确认重置</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 

            <!-- 注册 -->
            <div class="layui-tab-item">
                <form class="layui-form" action="" id='registerForm' lay-filter='registerForm'>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="hidden" name="action" value="student_register">
                            <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_register_code_nonce');?>">
                            <input type="text" name="user_login" lay-verify="phoneOrEmail" autocomplete="off" placeholder="手机号/邮箱" class="layui-input ">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="tel" name="verify_code" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input ">
                            <button class="getCodeBtn nl-dark-blue getCode" data-sendCodeCase="17">获取验证码</button>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="password" name="password" lay-verify="password" placeholder="设置密码,6位以上含字母及数字" autocomplete="off" class="layui-input ">
                        </div>
                    </div>
                                        
                    <p class="row-margin">
                        <span data-show="a1"  class="login-fast nl-dark-blue login-by">使用手机验证码快速登录</span>
                    </p>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <button class="layui-btn submitBtn  nl-bg-blue" type="button" id="registerBtn" lay-filter="registerBtn" lay-submit="">注 册</button>
                        </div>
                    </div>
                </form>
            </div> 
        </div> 
        <div class="nl-agreement">登录或注册即同意<span class="nl-dark-blue nl-ios-click">【脑力中国用户协议】</span></div>
        <ul style="margin-left: 0"  class="login-type">
            <li class="login-type-wrapper">
                <div class="login-type-logo">
                    <i class="iconfont">&#xe695;</i>
                </div>
                <div class="login-type-name">微信登录</div>
            </li>
            <li class="login-type-wrapper">
                <div class="login-type-logo">
                    <i class="iconfont">&#xe603;</i>
                </div>
                <div class="login-type-name">QQ登录</div>
            </li>
        </ul>
    </div>  
        <div class="width-margin width-margin-pc userAgreement" style='display:none'>
                <div class="head-tips">特别提示</div>
                    <p class="content-p indent">脑力(中国)运动开发有限公司（以下简称“脑力中国”）在此特别提醒您（用户）在注册成为用户之前，请您认真阅读本《用户协议》（以下简称“协议”）并审慎选择接受或不接受本协议内容。同时，脑力中国特别提醒用户注意本协议中免除脑力中国责任和限制用户权利的条款。未成年人应在法定监护人的陪同下阅读本协议。您的注册、登录、使用等行为将视为对本协议的全部接受，并同意接受本协议全部条款的约束。</p>
                    <p class="content-p indent">本协议约定脑力中国与用户之间关于“脑力中国”互联网平台服务（以下简称“服务”）的权利义务。“用户”是指注册、登录、使用本服务的主体。本协议内容将由脑力中国进行随时更新，更新后的协议条款一经公布即代替原来的协议条款，而不再另行单独通知，用户可在本网站查阅最新版协议条款。在协议条款更新后，如果用户不接受更新后的条款，请立即停止使用脑力中国提供的服务，用户继续使用脑力中国提供的服务将被视为接受更新后的全部协议内容。</p>
                    
                    <div class="content-title">一、账号注册</div>
                    <p class="content-p">1、用户在使用本服务前需要注册一个“脑力中国”平台帐号。“脑力中国”帐号应当使用手机号码绑定注册，请用户使用尚未与“脑力中国”帐号绑定的手机号码，以及未被脑力中国根据本协议封禁的手机号码注册“脑力中国”帐号。脑力中国可以根据用户需求或产品需要对帐号注册和绑定的方式进行变更，而无须事先通知用户。</p>
                    <p class="content-p">2、“脑力中国”包含基于地理位置的功能与服务，用户注册时应当授权脑力中国公开及使用其地理位置信息方可成功注册“脑力中国”帐号。故用户完成注册即表明用户同意脑力中国提取、公开及使用用户的地理位置信息。如用户需要终止向其他用户公开其地理位置信息，可自行设置为隐身状态。</p>
                    <p class="content-p">3、鉴于“脑力中国”帐号的绑定注册方式，您同意脑力中国在注册时将使用您提供的手机号码及/或自动提取您的手机号码及自动提取您的手机设备识别码等信息用于注册。</p>
                    <p class="content-p">4、在用户注册及使用本服务时，脑力中国需要搜集能识别用户身份的个人信息以便脑力中国可以在必要时联系用户，或为用户提供更好的使用体验。脑力中国搜集的信息包括但不限于用户的姓名、性别、年龄、出生日期、身份证号、地址、学校情况、公司情况、所属行业、兴趣爱好、常出没的地方、个人说明；脑力中国同意对这些信息的使用将受限于第三条用户个人隐私信息保护的约束。</p>
                    
                    <div class="content-title">二、服务内容</div>
                    <p class="content-p">1、本服务的具体内容由脑力中国根据实际情况提供，包括但不限于授权用户通过其帐号进行即时通讯、添加好友、加入群组、关注他人、发布留言、参加评测、脑力赛事等。脑力中国可以对其提供的服务予以变更，且脑力中国提供的服务内容可能随时变更；用户将会收到脑力中国关于服务变更的通知。</p>
                    <p class="content-p">2、脑力中国提供的服务包含免费服务与收费服务。用户可以通过付费方式购买收费服务，具体方式为：用户通过网上银行、支付宝或其他“脑力中国”平台提供的付费途径支付一定数额的人民币购买“脑力中国”平台的虚拟货币——脑币，然后根据脑力中国公布的资费标准以脑币购买用户欲使用的收费服务，从而获得收费服务使用权限。对于收费服务，脑力中国会在用户使用之前给予用户明确的提示，只有用户根据提示确认其同意按照前述支付方式支付费用并完成了支付行为，用户才能使用该等收费服务。支付行为的完成以银行或第三方支付平台生成“支付已完成”的确认通知为准。</p>
                    <div class="content-title">三、用户个人信息保护</div>
                    <p class="content-p">1、用户在注册帐号或使用本服务的过程中，可能需要填写或提交一些必要的个人信息，如法律法规、规章规范性文件（以下称“法律法规”）规定的需要填写的身份信息。如用户提交的信息不完整或不符合法律法规的规定，则用户可能无法使用本服务或在使用本服务的过程中受到限制。</p>
                    <p class="content-p">2、用户个人信息包括：1）用户自行提供的用户个人信息（如注册时填写的手机号码，电子邮件等个人信息，使用服务时提供的共享信息等）；2）其他方分享的用户个人信息；3）脑力中国为提供服务而合法收集的用户必要个人信息（如使用服务时系统自动采集的设备或软件信息，浏览历史信息，通讯时间信息等技术信息，用户开启定位功能并使用服务时的地理位置信息等）。</p>
                    <p class="content-p">其中个人隐私信息是指涉及用户个人身份或个人隐私的信息，比如，用户真实姓名、身份证号、手机号码、手机设备识别码、IP地址、用户聊天记录。非个人隐私信息是指用户对本服务的操作状态以及使用习惯等明确且客观反映在脑力中国服务器端的基本记录信息、个人隐私信息范围外的其它普通信息，以及用户同意公开的上述隐私信息。脑力中国保证在取得用户书面同意的情况下收集、使用或公开用户的个人隐私信息，用户同意脑力中国无需获得用户的另行确认与授权即可收集、使用或公开用户的非个人隐私信息。</p>
                    <p class="content-p">3、尊重用户个人信息的私有性是脑力中国的一贯制度，脑力中国将采取技术措施和其他必要措施，确保用户个人信息安全，防止在本服务中收集的用户个人信息泄露、毁损或丢失。在发生前述情形或者脑力中国发现存在发生前述情形的可能时，脑力中国将及时采取补救措施并告知用户，用户如发现存在前述情形亦需立即与脑力中国联系。</p>
                    <p class="content-p">4、脑力中国未经用户同意不向任何第三方公开、 透露用户个人隐私信息。但以下特定情形除外：</p>
                    <p class="content-p">(1) 脑力中国根据法律法规规定或有权机关的指示提供用户的个人隐私信息；</p>
                    <p class="content-p">(2) 由于用户将其用户密码告知他人或与他人共享注册帐户与密码，由此导致的任何个人信息的泄漏，或其他非因脑力中国原因导致的个人隐私信息的泄露；</p>
                    <p class="content-p">(3) 用户自行向第三方公开其个人隐私信息；</p>
                    <p class="content-p">(4) 用户与脑力中国及合作单位之间就用户个人隐私信息的使用公开达成约定，脑力中国因此向合作单位公开用户个人隐私信息；</p>
                    <p class="content-p">(5) 任何由于黑客攻击、电脑病毒侵入及其他不可抗力事件导致用户个人隐私信息的泄露；</p>
                    <p class="content-p">(6) 用户个人信息已经经过处理无法识别特定个人且不能复原。</p>
                    <p class="content-p">5、用户同意脑力中国可在以下事项中使用用户的个人隐私信息：</p>
                    <p class="content-p">(1) 脑力中国向用户及时发送重要通知，如软件更新、本协议条款的变更；</p>
                    <p class="content-p">(2) 脑力中国内部进行审计、数据分析和研究等，以改进脑力中国的产品、服务和与用户之间的沟通；</p>
                    <p class="content-p">(3) 依本协议约定，脑力中国管理、审查用户信息及进行处理措施；</p>
                    <p class="content-p">(4) 适用法律法规规定的其他事项。</p>
                    <p class="content-p">除上述事项外，如未取得用户事先同意，脑力中国不会将用户个人隐私信息使用于任何其他用途。</p>
                    <p class="content-p">6、脑力中国重视对未成年人个人信息的保护。脑力中国将依赖用户提供的个人信息判断用户是否为未成年人。任何18岁以下的未成年人注册帐号或使用本服务应事先取得家长或其法定监护人（以下简称"监护人"）的书面同意。除根据法律法规的规定及有权机关的指示披露外，脑力中国不会使用或向任何第三方透露未成年人的聊天记录及其他个人信息。除本协议约定的例外情形外，未经监护人事先同意，脑力中国不会使用或向任何第三方透露未成年人的个人信息。任何18岁以下的用户不得下载和使用脑力中国通过脑力中国软件提供的网络游戏。</p>
                    <p class="content-p">7、因脑力中国提供的部分服务系基于地理位置提供的赛事或训练，用户确认，其地理位置信息为非个人隐私信息，用户成功注册“脑力中国”帐号视为确认授权脑力中国提取、公开及使用用户的地理位置信息。用户地理位置信息将作为用户公开资料之一，由脑力中国向其他用户公开以便脑力中国向用户提供基于地理位置的相关服务。如用户需要终止向其他用户公开其地理位置信息，可随时自行设置为隐身状态。</p>
                    <p class="content-p">8、为了改善脑力中国的技术和服务，向用户提供更好的服务体验，脑力中国或可会自行收集使用或向第三方提供用户的非个人隐私信息。</p>
                    <p class="content-p">9、脑力中国保证在合法、正当与必要的原则下收集、使用或者公开用户个人信息且不会收集与提供的服务无关的用户个人信息。</p>
                    <p class="content-p">10、脑力中国十分注重保护用户的个人隐私，并制定了《脑力中国隐私权政策》（点击查看），用户亦可以通过“设置”页面里的“帮助”来进行具体查看，用户确认并同意使用脑力中国提供的服务将被视为接受《脑力中国隐私权政策》。</p>
                    
                    <div class="content-title">四、内容规范</div>
                    <p class="content-p">1、本条所述内容是指用户使用本服务过程中所制作、上载、复制、发布、传播的任何内容，包括但不限于帐号头像、名称、用户说明等注册信息及认证资料，或文字、语音、图片、视频、图文等发送、回复或自动回复消息和相关链接页面，以及其他使用帐号或本服务所产生的内容。</p>
                    <p class="content-p">2、用户不得利用“脑力中国”帐号或本服务制作、上载、复制、发布、传播如下法律、法规和政策禁止的内容：</p>
                    <p class="content-p">(1) 反对宪法所确定的基本原则的；</p>
                    <p class="content-p">(2) 危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；</p>
                    <p class="content-p">(3) 损害国家荣誉和利益的；</p>
                    <p class="content-p">(4) 煽动民族仇恨、民族歧视，破坏民族团结的；</p>
                    <p class="content-p">(5) 破坏国家宗教政策，宣扬邪教和封建迷信的；</p>
                    <p class="content-p">(6) 散布谣言，扰乱社会秩序，破坏社会稳定的；</p>
                    <p class="content-p">(7) 散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；</p>
                    <p class="content-p">(8) 侮辱或者诽谤他人，侵害他人合法权益的；</p>
                    <p class="content-p">(9) 不遵守法律法规底线、社会主义制度底线、国家利益底线、公民合法权益底线、社会公共秩序底线、道德风尚底线和信息真实性底线的“七条底线”要求的；</p>
                    <p class="content-p">(10) 含有法律、行政法规禁止的其他内容的信息。</p>
                    <p class="content-p">3、用户不得利用“脑力中国”帐号或本服务制作、上载、复制、发布、传播如下干扰“脑力中国”正常运营，以及侵犯其他用户或第三方合法权益的内容：</p>
                    <p class="content-p">(1) 含有任何性或性暗示的；</p>
                    <p class="content-p">(2) 含有辱骂、恐吓、威胁内容的；</p>
                    <p class="content-p">(3) 含有骚扰、垃圾广告、恶意信息、诱骗信息的；</p>
                    <p class="content-p">(4) 涉及他人隐私、个人信息或资料的；</p>
                    <p class="content-p">(5) 侵害他人名誉权、肖像权、知识产权、商业秘密等合法权利的；</p>
                    <p class="content-p">(6) 含有其他干扰本服务正常运营和侵犯其他用户或第三方合法权益内容的信息。</p>
                    
                    <div class="content-title">五、使用规则</div>
                    <p class="content-p">1、用户在本服务中或通过本服务所传送、发布的任何内容并不反映或代表，也不得被视为反映或代表脑力中国的观点、立场或政策，脑力中国对此不承担任何责任。</p>
                    <p class="content-p">2、用户不得利用“脑力中国”帐号或本服务进行如下行为：</p>
                    <p class="content-p">(1) 提交、发布虚假信息，或盗用他人头像或资料，冒充、利用他人名义的；</p>
                    <p class="content-p">(2) 强制、诱导其他用户关注、点击链接页面或分享信息的；</p>
                    <p class="content-p">(3) 虚构事实、隐瞒真相以误导、欺骗他人的；</p>
                    <p class="content-p">(4) 利用技术手段批量建立虚假帐号的；</p>
                    <p class="content-p">(5) 利用“脑力中国”帐号或本服务从事任何违法犯罪活动的；</p>
                    <p class="content-p">(6) 制作、发布与以上行为相关的方法、工具，或对此类方法、工具进行运营或传播，无论这些行为是否为商业目的；</p>
                    <p class="content-p">(7) 其他违反法律法规规定、侵犯其他用户合法权益、干扰“脑力中国”正常运营或脑力中国未明示授权的行为。</p>
                    <p class="content-p">3、用户须对利用“脑力中国”帐号或本服务传送信息的真实性、合法性、无害性、准确性、有效性等全权负责，与用户所传播的信息相关的任何法律责任由用户自行承担，与脑力中国无关。如因此给脑力中国或第三方造成损害的，用户应当依法予以赔偿。</p>
                    <p class="content-p">4、脑力中国提供的服务中可能包括广告，用户同意在使用过程中显示脑力中国和第三方供应商、合作伙伴提供的广告。除法律法规明确规定外，用户应自行对依该广告信息进行的交易负责，对用户因依该广告信息进行的交易或前述广告商提供的内容而遭受的损失或损害，脑力中国不承担任何责任。</p>
                    <p class="content-p">5、除非脑力中国书面许可，用户不得从事下列任一行为：</p>
                    <p class="content-p">(1) 删除软件及其副本上关于著作权的信息；</p>
                    <p class="content-p">(2) 对软件进行反向工程、反向汇编、反向编译，或者以其他方式尝试发现软件的源代码；</p>
                    <p class="content-p">(3) 对脑力中国拥有知识产权的内容进行使用、出租、出借、复制、修改、链接、转载、汇编、发表、出版、建立镜像站点等；</p>
                    <p class="content-p">(4) 对软件或者软件运行过程中释放到任何终端内存中的数据、软件运行过程中客户端与服务器端的交互数据，以及软件运行所必需的系统数据，进行复制、修改、增加、删除、挂接运行或创作任何衍生作品，形式包括但不限于使用插件、外挂或非经脑力中国授权的第三方工具/服务接入软件和相关系统；</p>
                    <p class="content-p">(5) 通过修改或伪造软件运行中的指令、数据，增加、删减、变动软件的功能或运行效果，或者将用于上述用途的软件、方法进行运营或向公众传播，无论这些行为是否为商业目的；</p>
                    <p class="content-p">(6) 通过非脑力中国开发、授权的第三方软件、插件、外挂、系统，登录或使用脑力中国软件及服务，或制作、发布、传播非脑力中国开发、授权的第三方软件、插件、外挂、系统。</p>
                    
                    <div class="content-title">六、虚拟货币</div>
                    <p class="content-p">1、脑币是“脑力中国”平台内的虚拟货币。脑币可用于购买“脑力中国”平台的增值服务，包括但不限于在线课程、培训、考级、比赛及会员服务，除此外，不得用于其他任何用途。该等增值服务的价格均以脑币为单位，具体价格信息将由脑力中国自行决定并在相关服务页面上显示。</p>
                    <p class="content-p">2、脑币和人民币的兑换比例依用户购买渠道的不同而有不同的兑换比例，具体兑换比例以用户购买脑币相关渠道服务页面显示为准。脑力中国有权根据运营情况随时变更上述兑换比例，并将在用户购买脑币相关渠道服务页面显示。</p>
                    <p class="content-p">3、用户默认已开通脑力中国币账户，可进行脑币购买（下称“充值”）和消费。用户可在设置页面查询到脑币余额、购买记录和消费记录。脑币相关信息将不作为公开信息。</p>
                    <p class="content-p">4、用户可以通过网上银行、支付宝或其他“脑力中国”平台提供的充值途径为脑力中国账户进行充值。用户使用脑币购买相关收费服务后，可将相关收费服务赠与其他用户。用户确认不会以非法方式或者使用非平台所指定的充值途径进行充值,如果用户违规使用非脑力中国认可的充值途径非法充值/购买脑币，则脑力中国不保证充值顺利或正确完成，若因此造成用户权益受损，脑力中国将不会作出任何补偿或赔偿，脑力中国同时保留随时终止用户脑力中国账号资格及使用各项充值服务的权利，并进行相应惩罚。</p>
                    <p class="content-p">5、用户确认在进行充值前已经仔细确认过自己的账号并仔细选择了相关操作选项，若因用户自身输入账号错误、操作不当或不了解充值计费方式等因素造成充错账号、错选充值种类等情形而损害自身权益的，脑力中国将不会作出任何补偿或赔偿。</p>
                    <p class="content-p">6、用户确认，脑币一经充值成功，除法律法规明确规定外，在任何情况下不能兑换为法定货币，不能转让他人。除法律法规明确规定外，脑币账户充值完成后，脑力中国不予退款。</p>
                    <p class="content-p">7、用户确认，脑币只能用于购买“脑力中国”平台上的各类增值服务，任何情况下不得与脑力中国以外的第三方进行脑币交易，亦不得在除“脑力中国”平台以外的第三方平台（如淘宝）上进行交易；如违反前述约定，造成用户或第三方任何损失，脑力中国不负任何责任，且如脑力中国有理由怀疑用户的脑力中国账户或使用情况有作弊或异常状况，脑力中国将拒绝该用户使用脑币进行支付，直至按本协议约定采取相关封禁措施。</p>
                    <p class="content-p">8、脑力中国有权基于交易安全等方面的考虑不时设定或修改涉及交易的相关事项，包括但不限于交易限额、交易次数等。用户了解并确认脑力中国的前述设定或修改可能对用户的交易产生一定的不便，用户对此没有异议。</p>
                    <p class="content-p">9、用户确认，除法律法规明确规定或本协议另有约定外，用户已购买的任何收费服务不能以任何理由退购（即退换成脑币或法定货币）或调换成其他服务。</p>
                    <p class="content-p">10、脑力中国不鼓励未成年人使用虚拟货币服务，未成年人应请监护人操作或在监护人明示同意下操作，否则不得使用本服务。</p>
                    <p class="content-p">11、因用户自身的原因导致脑力中国无法提供脑币购买服务或提供脑币购买服务时发生任何错误而产生的任何损失或责任，由用户自行负责，脑力中国不承担责任，包括但不限于：</p>
                    <p class="content-p">(1)因用户的脑力中国账号丢失、被封禁或冻结；</p>
                    <p class="content-p">(2)用户将密码告知他人导致的财产损失；</p>
                    <p class="content-p">(3)因用户绑定的第三方支付机构账户的原因导致的任何损失或责任；</p>
                    <p class="content-p">(4)其他用户故意或者重大过失或者违反法律法规导致的财产损失。</p>
                    <p class="content-p">12、用户在使用脑力中国提供的服务时，如出现违反国家法律法规、本协议约定或其他本平台对用户的管理规定的情形，脑力中国有权暂时或永久封禁用户的账号。账号封禁后至解禁（如有）前，用户账户上的剩余脑力中国币将被暂时冻结或全部扣除，不可继续用于购买平台上的虚拟产品或服务，同时不予返还用户购买脑力中国币时的现金价值。</p>
                    <p class="content-p">13、用户确认并同意如用户主动注销账号，则用户已充值到账的脑力中国币，购买的虚拟礼物，游戏币以及会员权益等视为自动放弃，脑力中国不予返还相应的现金价值，也不会作出任何补偿。</p>
                    
                    <div class="content-title">七、账户管理</div>
                    <p class="content-p">1、 “脑力中国”帐号的所有权归脑力中国所有，用户完成申请注册手续后，获得“脑力中国”帐号的使用权，该使用权仅属于初始申请注册人，禁止赠与、借用、租用、转让或售卖。脑力中国因经营需要，有权回收用户的“脑力中国”帐号。</p>
                    <p class="content-p">2、用户可以通过1）查看与编辑个人资料页，2）“设置”页面里的“账号与安全”页面来查询、更改、删除、注销“脑力中国”帐户上的个人资料、注册信息及传送内容等，但需注意，删除有关信息的同时也会删除用户储存在系统中的文字和图片。用户需承担该风险。</p>
                    <p class="content-p">3、用户有责任妥善保管注册帐号信息及帐号密码的安全，因用户保管不善可能导致遭受盗号或密码失窃，责任由用户自行承担。用户需要对注册帐号以及密码下的行为承担法律责任。用户同意在任何情况下不使用其他用户的帐号或密码。在用户怀疑他人使用其帐号或密码时，用户同意立即通知脑力中国。</p>
                    <p class="content-p">4、用户应遵守本协议的各项条款，正确、适当地使用本服务，如因用户违反本协议中的任何条款，脑力中国在通知用户后有权依据协议中断或终止对违约用户“脑力中国”帐号提供服务。同时，脑力中国保留在任何时候收回“脑力中国”帐号、用户名的权利。</p>
                    <p class="content-p">5、如用户注册“脑力中国”帐号后一年不登录，通知用户后，脑力中国可以收回该帐号，以免造成资源浪费，由此造成的不利后果由用户自行承担。</p>
                    <p class="content-p">6、用户可以通过“设置”页面里的“账号与安全”页面来进行账号注销服务，用户确认注销账号是不可恢复的操作，用户应自行备份与脑力中国账号相关的信息和数据，用户确认操作之前与脑力中国账号相关的所有服务均已进行妥善处理。用户确认并同意注销账号后并不代表本脑力中国账号注销前的账号行为和相关责任得到豁免或减轻，如在注销期间，用户的账号被他人投诉、被国家机关调查或者正处于诉讼、仲裁程序中，脑力中国有限自行终止用户的账号注销并无需另行得到用户的同意。</p>
                    
                    <div class="content-title">八、数据储存</div>
                    <p class="content-p">1、脑力中国不对用户在本服务中相关数据的删除或储存失败负责。</p>
                    <p class="content-p">2、脑力中国可以根据实际情况自行决定用户在本服务中数据的最长储存期限，并在服务器上为其分配数据最大存储空间等。用户可根据自己的需要自行备份本服务中的相关数据。</p>
                    <p class="content-p">3、如用户停止使用本服务或本服务终止，脑力中国可以从服务器上永久地删除用户的数据。本服务停止、终止后，脑力中国没有义务向用户返还任何数据。</p>
                    <div class="content-title">九、风险承担</div>
                    <p class="content-p">1、用户理解并同意，“脑力中国”仅为用户提供信息分享、传送及获取的平台，用户必须为自己注册帐号下的一切行为负责，包括用户所传送的任何内容以及由此产生的任何后果。用户应对“脑力中国”及本服务中的内容自行加以判断，并承担因使用内容而引起的所有风险，包括因对内容的正确性、完整性或实用性的依赖而产生的风险。脑力中国无法且不会对因用户行为而导致的任何损失或损害承担责任。</p>
                    <p class="content-p">如果用户发现任何人违反本协议约定或以其他不当的方式使用本服务，请立即向脑力中国举报或投诉，脑力中国将依本协议约定进行处理。</p>
                    <p class="content-p">2、用户理解并同意，因业务发展需要，脑力中国保留单方面对本服务的全部或部分服务内容变更、暂停、终止或撤销的权利，用户需承担此风险。</p>

                    <div class="content-title">十、知识产权声明</div>
                    <p class="content-p">1、除本服务中涉及广告的知识产权由相应广告商享有外，脑力中国在本服务中提供的内容（包括但不限于网页、文字、图片、音频、视频、图表等）的知识产权均归脑力中国所有，但用户在使用本服务前对自己发布的内容已合法取得知识产权的除外。</p>
                    <p class="content-p">2、除另有特别声明外，脑力中国提供本服务时所依托软件的著作权、专利权及其他知识产权均归脑力中国所有。</p>
                    <p class="content-p">3、脑力中国在本服务中所涉及的图形、文字或其组成，以及其他脑力中国标志及产品、服务名称（以下统称“脑力中国标识”），其著作权或商标权归脑力中国所有。未经脑力中国事先书面同意，用户不得将脑力中国标识以任何方式展示或使用或作其他处理，也不得向他人表明用户有权展示、使用、或其他有权处理脑力中国标识的行为。</p>
                    <p class="content-p">4、上述及其他任何脑力中国或相关广告商依法拥有的知识产权均受到法律保护，未经脑力中国或相关广告商书面许可，用户不得以任何形式进行使用或创造相关衍生作品。</p>

                    <div class="content-title">十一、法律责任</div>
                    <p class="content-p">1、如果脑力中国发现或收到他人举报或投诉用户违反本协议约定的，脑力中国有权不经通知随时对相关内容，包括但不限于用户资料、聊天记录进行审查、删除，并视情节轻重对违规帐号处以包括但不限于警告、帐号封禁 、设备封禁 、功能封禁 的处罚，且通知用户处理结果。</p>
                    <p class="content-p">2、因违反用户协议被封禁的用户，可以自行到 脑力中国官方平台 查询封禁期限，并在封禁期限届满后自助解封。其中，被实施功能封禁的用户会在封禁期届满后自动恢复被封禁功能。被封禁用户可向脑力中国网站相关页面提交申诉，脑力中国将对申诉进行审查，并自行合理判断决定是否变更处罚措施。</p>
                    <p class="content-p">3、用户理解并同意，脑力中国有权依合理判断对违反有关法律法规或本协议规定的行为进行处罚，对违法违规的任何用户采取适当的法律行动，并依据法律法规保存有关信息向有关部门报告等，用户应承担由此而产生的一切法律责任。</p>
                    <p class="content-p">4、用户理解并同意，因用户违反本协议约定，导致或产生的任何第三方主张的任何索赔、要求或损失，包括合理的律师费，用户应当赔偿脑力中国与合作公司、关联公司，并使之免受损害。</p>
       
                    <div class="content-title">十二、不可抗力及其他免责事由</div>
                    <p class="content-p">1、用户理解并确认，在使用本服务的过程中，可能会遇到不可抗力等风险因素，使本服务发生中断。不可抗力是指不能预见、不能克服并不能避免且对一方或双方造成重大影响的客观事件，包括但不限于自然灾害如洪水、地震、瘟疫流行和风暴等以及社会事件如战争、动乱、政府行为等。出现上述情况时，脑力中国将努力在第一时间与相关单位配合，及时进行修复，但是由此给用户或第三方造成的损失，脑力中国及合作单位在法律允许的范围内免责。</p>
                    <p class="content-p">2、本服务同大多数互联网服务一样，受包括但不限于用户原因、网络服务质量、社会环境等因素的差异影响，可能受到各种安全问题的侵扰，如他人利用用户的资料，造成现实生活中的骚扰；用户下载安装的其它软件或访问的其他网站中含有“特洛伊木马”等病毒，威胁到用户的计算机信息和数据的安全，继而影响本服务的正常使用等等。用户应加强信息安全及使用者资料的保护意识，要注意加强密码保护，以免遭致损失和骚扰。</p>
                    <p class="content-p">3、用户理解并确认，本服务存在因不可抗力、计算机病毒或黑客攻击、系统不稳定、用户所在位置、用户关机以及其他任何技术、互联网络、通信线路原因等造成的服务中断或不能满足用户要求的风险，因此导致的用户或第三方任何损失，脑力中国不承担任何责任。</p>
                    <p class="content-p">4、用户理解并确认，在使用本服务过程中存在来自任何他人的包括误导性的、欺骗性的、威胁性的、诽谤性的、令人反感的或非法的信息，或侵犯他人权利的匿名或冒名的信息，以及伴随该等信息的行为，因此导致的用户或第三方的任何损失，脑力中国不承担任何责任。</p>
                    <p class="content-p">5、用户理解并确认，脑力中国需要定期或不定期地对“脑力中国”平台或相关的设备进行检修或者维护，如因此类情况而造成服务在合理时间内的中断，脑力中国无需为此承担任何责任，但脑力中国应事先进行通告。</p>
                    <p class="content-p">6、脑力中国依据法律法规、本协议约定获得处理违法违规或违约内容的权利，该权利不构成脑力中国的义务或承诺，脑力中国不能保证及时发现违法违规或违约行为或进行相应处理。</p>
                    <p class="content-p">7、用户理解并确认，对于脑力中国向用户提供的下列产品或者服务的质量缺陷及其引发的任何损失，脑力中国无需承担任何责任：</p>
                    <p class="content-p">(1) 脑力中国向用户免费提供的服务；</p>
                    <p class="content-p">(2) 脑力中国向用户赠送的任何产品或者服务。</p>
                    <p class="content-p">8、在任何情况下，脑力中国均不对任何间接性、后果性、惩罚性、偶然性、特殊性或刑罚性的损害，包括因用户使用“脑力中国”或本服务而遭受的利润损失，承担责任（即使脑力中国已被告知该等损失的可能性亦然）。尽管本协议中可能含有相悖的规定，脑力中国对用户承担的全部责任，无论因何原因或何种行为方式，始终不超过用户因使用脑力中国提供的服务而支付给脑力中国的费用(如有)。</p>
       
                    <div class="content-title">十三、服务的变更、中断、终止</div>
                    <p class="content-p">1、鉴于网络服务的特殊性，用户同意脑力中国有权随时变更、中断或终止部分或全部的服务（包括收费服务）。脑力中国变更、中断或终止的服务，脑力中国应当在变更、中断或终止之前通知用户，并应向受影响的用户提供等值的替代性的服务；如用户不愿意接受替代性的服务，如果该用户已经向脑力中国支付的脑力中国币，脑力中国应当按照该用户实际使用服务的情况扣除相应脑力中国币之后将剩余的脑力中国币退还用户的脑力中国币账户中。</p>
                    <p class="content-p">2、如发生下列任何一种情形，脑力中国有权变更、中断或终止向用户提供的免费服务或收费服务，而无需对用户或任何第三方承担任何责任：</p>
                    <p class="content-p">(1) 根据法律规定用户应提交真实信息，而用户提供的个人资料不真实、或与注册时信息不一致又未能提供合理证明；</p>
                    <p class="content-p">(2) 用户违反相关法律法规或本协议的约定；</p>
                    <p class="content-p">(3) 按照法律规定或有权机关的要求；</p>
                    <p class="content-p">(4) 出于安全的原因或其他必要的情形。</p>
       
                    <div class="content-title">十四、其他</div>
                    <p class="content-p">1、脑力中国郑重提醒用户注意本协议中免除脑力中国责任和限制用户权利的条款，请用户仔细阅读，自主考虑风险。未成年人应在法定监护人的陪同下阅读本协议。</p>
                    <p class="content-p">2、本协议的效力、解释及纠纷的解决，适用于中华人民共和国法律。若用户和脑力中国之间发生任何纠纷或争议，首先应友好协商解决，协商不成的，用户同意将纠纷或争议提交脑力中国住所地有管辖权的人民法院管辖。</p>
                    <p class="content-p">3、本协议的任何条款无论因何种原因无效或不具可执行性，其余条款仍有效，对双方具有约束力。</p>
                    <p class="content-p">4、由于互联网高速发展，您与脑力中国签署的本协议列明的条款可能并不能完整罗列并覆盖您与脑力中国所有权利与义务，现有的约定也不能保证完全符合未来发展的需求。因此，脑力中国隐私权政策、脑力中国平台行为规范等均为本协议的补充协议，与本协议不可分割且具有同等法律效力。如您使用脑力中国平台服务，视为您同意上述补充协议。</p>
                </div>
</div>
    <script>
jQuery(function($) {
    initHeight=function(){
        var window_height=$(window).height();
        var top=parseInt($('#page').css('top'));
        var height=window_height-top+'px'
        $('.wrapper_content').css('minHeight',height)
    };
    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        initHeight();//手机端最小高度为屏幕高度
    }
    setLocal=function(key,value) {
        if(window.localStorage){
            var storage=window.localStorage;
            var v=JSON.stringify(value)
            storage.setItem(key,escape(v));
        }
    }
    getLocal=function(key) {
        if(window.localStorage){
            var storage=window.localStorage;
            var v=unescape(storage.getItem(key))
            return JSON.parse(v);
        }else{
            return null;
        }
    }
        sendloginAjax=function(url,formData){
            //type：确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType:'json',
                timeout:3000,
                success: function(data, textStatus, jqXHR){
                    // console.log(data)
                    $.alerts(data.data.info)
                    if(data.success){
                        // window.localStorage.removeItem(formData.user_login);//登陆成功删除记录
                        if(data.data.url){
                            window.location.href=data.data.url
                            setTimeout(function(){
                                window.location.href=data.data.url
                            },300)
                        }                        
                    }else{//登陆失败。记录登录时间
                    }


                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                    // 通常 textStatus 和 errorThrown 之中
                    // 只有一个会包含信息
                    // 调用本次AJAX请求时传递的options参数
                    console.log(XMLHttpRequest, textStatus, errorThrown)
                }
            });
        } 
        layui.use(['element','form'], function(){
            var form = layui.form;
            var element = layui.element;
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules); 
            // 监听提交
            form.on('submit(loginFormFastBtn)', function(data){//快速登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(loginFormForgetBtn)', function(data){//重置密码
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(loginFormPswBtn)', function(data){//账号密码登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(registerBtn)', function(data){//注册
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            element.on('tab(tabs)', function(){//tabs
                var left=$(this).position().left;
                var html=$(this).html();
                var css=''
                if($(this).index()==0){
                    css='22.5px 0 0 22.5px'
                }else{
                    css='0px 22.5px 22.5px 0'
                }
                $('.nl-transform').css({
                    'transform':'translate3d('+left+'px, 0px, 0px)',
                    'border-radius':css
                }).html(html)
            });
        });

//-----------------获取验证码-------------------- 
        
        function time(wait,o){//倒计时
            if (wait == 0) {  
                o.removeAttr("disabled");            
                o.text("获取短信验证码")  
                wait = 60;  
            } else {  
                o.attr("disabled", true);  
                o.text("重新发送(" + wait + ")")

                wait--;  
                setTimeout(function() {  
                    time(wait,o)  
                },  
                1000)  
            }  
        }
        $('.getCode').click(function(){//获取验证码
            // var value=$("#loginForm input[name='user_login']").val()
            var dom=$(this).parents('form').find("input[name='user_login']")
            var value=dom.val()
            var allRules=$.validationLayui.allRules;//全局正则配置
            var phone=allRules['phone'][0];
            var email=allRules['email'][0];
            var layVerify=dom.attr('lay-verify')
            var message=allRules[layVerify][1];
            var template=parseInt($(this).attr('data-sendCodeCase'));

            if(layVerify=='phone'){//手机登录
                if(phone.test(value)){
                    var formData=$(this).parents('form').serializeObject();
                    var getTimestamp=new Date().getTime()
                    var action='get_sms_code'
                    var data={
                        action:action,
                        mobile:formData.user_login,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                    var wait=60;  
                    time(wait,$(this))
                }else{
                    // $(this).parents('form').find("input[name='user_login']").focus()
                    $.alerts(message)
                    return false
                }
            }else if(layVerify=='phoneOrEmail'){//手机或邮箱登录
                message=allRules['phoneOrEmail'];
                if(phone.test(value) || email.test(value)){
                    var formData=$(this).parents('form').serializeObject();
                    var getTimestamp=new Date().getTime()
                    var action='get_sms_code'
                    if(phone.test(value)){//手机号码登录
                        action='get_sms_code'
                    }else if(email.test(value)){//邮箱登录
                        action='get_smtp_code'    
                    }
                    var data={
                        action:action,
                        user_login:formData.user_login,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                    var wait=60;  
                    time(wait,$(this))
                    return false
                }else{
                    // $(this).parents('form').find("input[name='user_login']").focus()
                    $.alerts(message)
                    return false
                }
            }
        })
        $('.login-by-code').click(function(){//快速登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        $('.login-by-psw').click(function(){//密码登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
        })
        
        $('.login-by-reset').click(function(){//忘记密码
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('重置密码')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        
        $('.login-fast').click(function(){//注册tab页返回快速登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');

            //tabs切换
            $('.layui-tab-title li').eq(0).click()
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        $('.nl-agreement .nl-ios-click').click(function(){
                var html=$('.userAgreement').html(); 
                layer.open({
                    type: 1
                    ,title: false //不显示标题栏
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                    ,btn: ['知道了']
                    ,btnAlign: 'c'
                    ,moveType: 1 //拖拽模式，0或者1
                    ,content: '<div class="width-margin width-margin-pc userAgreement-content">'+html+'</div>'
                    ,success: function(layero){
                        
                    },
                    cancel: function(index, layero){
                    layer.closeAll();
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                });
            })
})
     </script>