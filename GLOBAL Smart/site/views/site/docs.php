<?php
    use yii\helpers\Url;
    $this->title = \Yii::t("app", 'Documents');
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper-content">
<article class="article  box">
    <h4 class="header-title"><?= \Yii::t("app","All documentation for the project") ?></h4>

    <div class="box-body table-responsive no-padding ">
        <table class="table docs-table m-0">
            <thead>
                <tr>
                    <th><span><?= \Yii::t("app","Name document")?></span></th>
                    <th><span><?= \Yii::t("app","Download link")?></span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Terms and Conditions</td>
                    
                    <td><a target="_blank" href="/assets/docs/TermsConditionsENG.docx" download="TermsConditionsENG.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>Privacy Policy</td>
                    <td><a target="_blank" href="/assets/docs/PrivacyPolicy.docx" download="PrivacyPolicy.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>Policy Refund</td>
                    
                    <td><a target="_blank" href="/assets/docs/RefundPolicy.docx" download="RefundPolicy.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>Token Sale Agreement</td>
                    <td><a target="_blank" href="/assets/docs/Tokensaleagreement.docx" download="Tokensaleagreement.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>KYC и AML</td>
                    
                    <td><a target="_blank" href="/assets/docs/AML.docx" download="AML.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>Whitepapper</td>
                    
                    <td><a target="_blank" href="/assets/docs/WP_en.docx" download="WP_en.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
                <tr>
                    <td>Terms and conditions</td>
                    <td><a target="_blank" href="/assets/docs/TermsConditionsENG.docx" download="TermsConditionsENG.docx"><?= \Yii::t("app", "Download")?></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</article>
</div>