<div class="account-create">
    <div class="page-title">
        <h1><?php echo $this->__('Create a Vendor Account') ?></h1>
    </div>
    <?php echo $this->getChildHtml('form_fields_before')?>
    <?php echo $this->getMessagesBlock()->toHtml() ?>
    <?php /* Extensions placeholder */ ?>
    <?php echo $this->getChildHtml('vendor.form.register.extra')?>
    <form action="<?php echo $this->getPostActionUrl() ?>" method="post" id="form-validate">
        <div class="fieldset">
        
            <input type="hidden" name="success_url" value="<?php echo $this->getSuccessUrl() ?>" />
            <input type="hidden" name="error_url" value="<?php echo $this->getErrorUrl() ?>" />
            <input type="hidden" name="form_key" value="<?php echo Mage::getSingleton('core/session')->getFormKey() ?>" />
            <h2 class="legend"><?php echo $this->__('Personal Information') ?></h2>
            <ul class="form-list">
                <li class="fields">
                    <?php echo $this->getLayout()->createBlock('vendor/widget_name')->setObject($this->getFormData())->setForceUsevendorAttributes(true)->toHtml() ?>
                </li>
                <li>
                    <label for="email_address" class="required"><em>*</em><?php echo $this->__('Email Address') ?></label>
                    <div class="input-box">
                        <input type="text" name="email" id="email_address" value="<?php echo $this->escapeHtml($this->getFormData()->getEmail()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Email Address')) ?>" class="input-text validate-email required-entry" />
                    </div>
                </li>
                <?php if ($this->isNewsletterEnabled()): ?>
                <li class="control">
                    <div class="input-box">
                        <input type="checkbox" name="is_subscribed" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Sign Up for Newsletter')) ?>" value="1" id="is_subscribed"<?php if($this->getFormData()->getIsSubscribed()): ?> checked="checked"<?php endif; ?> class="checkbox" />
                    </div>
                    <label for="is_subscribed"><?php echo $this->__('Sign Up for Newsletter') ?></label>
                    <?php /* Extensions placeholder */ ?>
                    <?php echo $this->getChildHtml('vendor.form.register.newsletter')?>
                </li>
                <?php endif ?>
            <?php $_dob = $this->getLayout()->createBlock('vendor/widget_dob') ?>
            <?php if ($_dob->isEnabled()): ?>
                <li><?php echo $_dob->setDate($this->getFormData()->getDob())->toHtml() ?></li>
            <?php endif ?>
            <?php $_taxvat = $this->getLayout()->createBlock('vendor/widget_taxvat') ?>
            <?php if ($_taxvat->isEnabled()): ?>
                <li><?php echo $_taxvat->setTaxvat($this->getFormData()->getTaxvat())->toHtml() ?></li>
            <?php endif ?>
            <?php $_gender = $this->getLayout()->createBlock('vendor/widget_gender') ?>
            <?php if ($_gender->isEnabled()): ?>
                <li><?php echo $_gender->setGender($this->getFormData()->getGender())->toHtml() ?></li>
            <?php endif ?>
            </ul>
        </div>
    <?php if($this->getShowAddressFields()): ?>
        <div class="fieldset">
            <input type="hidden" name="create_address" value="1" />
            <h2 class="legend"><?php echo $this->__('Address Information') ?></h2>
            <ul class="form-list">
                <li class="fields">
                    <div class="field">
                        <label for="company"><?php echo $this->__('Company') ?></label>
                        <div class="input-box">
                            <input type="text" name="company" id="company" value="<?php echo $this->escapeHtml($this->getFormData()->getCompany()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Company')) ?>" class="input-text <?php echo $this->helper('vendor/address')->getAttributeValidationClass('company') ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <label for="telephone" class="required"><em>*</em><?php echo $this->__('Telephone') ?></label>
                        <div class="input-box">
                            <input type="text" name="telephone" id="telephone" value="<?php echo $this->escapeHtml($this->getFormData()->getTelephone()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Telephone')) ?>" class="input-text <?php echo $this->helper('vendor/address')->getAttributeValidationClass('telephone') ?>" />
                        </div>
                    </div>
                </li>
            <?php $_streetValidationClass = $this->helper('vendor/address')->getAttributeValidationClass('street'); ?>
                <li class="wide">
                    <label for="street_1" class="required"><em>*</em><?php echo $this->__('Street Address') ?></label>
                    <div class="input-box">
                        <input type="text" name="street[]" value="<?php echo $this->escapeHtml($this->getFormData()->getStreet(1)) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Street Address')) ?>" id="street_1" class="input-text <?php echo $_streetValidationClass ?>" />
                    </div>
                </li>
            <?php $_streetValidationClass = trim(str_replace('required-entry', '', $_streetValidationClass)); ?>
            <?php for ($_i = 2, $_n = $this->helper('vendor/address')->getStreetLines(); $_i <= $_n; $_i++): ?>
                <li class="wide">
                    <div class="input-box">
                        <input type="text" name="street[]" value="<?php echo $this->escapeHtml($this->getFormData()->getStreet($_i)) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Street Address %s', $_i)) ?>" id="street_<?php echo $_i ?>" class="input-text <?php echo $_streetValidationClass ?>" />
                    </div>
                </li>
            <?php endfor; ?>
                <li class="fields">
                    <div class="field">
                        <label for="city" class="required"><em>*</em><?php echo $this->__('City') ?></label>
                        <div class="input-box">
                            <input type="text" name="city" value="<?php echo $this->escapeHtml($this->getFormData()->getCity()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('City')) ?>" class="input-text <?php echo $this->helper('vendor/address')->getAttributeValidationClass('city') ?>" id="city" />
                        </div>
                    </div>
                    <div class="field">
                        <label for="region_id" class="required"><em>*</em><?php echo $this->__('State/Province') ?></label>
                        <div class="input-box">
                            <select id="region_id" name="region_id" title="<?php echo Mage::helper('core')->quoteEscape($this->__('State/Province')) ?>" class="validate-select" style="display:none;">
                                <option value=""><?php echo $this->__('Please select region, state or province') ?></option>
                            </select>
                            <script type="text/javascript">
                            //<![CDATA[
                                $('region_id').setAttribute('defaultValue', "<?php echo $this->getFormData()->getRegionId() ?>");
                            //]]>
                            </script>
                            <input type="text" id="region" name="region" value="<?php echo $this->escapeHtml($this->getRegion()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('State/Province')) ?>" class="input-text <?php echo $this->helper('vendor/address')->getAttributeValidationClass('region') ?>" style="display:none;" />
                        </div>
                    </div>
                </li>
                <li class="fields">
                    <div class="field">
                        <label for="zip" class="required"><em>*</em><?php echo $this->__('Zip/Postal Code') ?></label>
                        <div class="input-box">
                            <input type="text" name="postcode" value="<?php echo $this->escapeHtml($this->getFormData()->getPostcode()) ?>" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Zip/Postal Code')) ?>" id="zip" class="input-text validate-zip-international <?php echo $this->helper('vendor/address')->getAttributeValidationClass('postcode') ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <label for="country" class="required"><em>*</em><?php echo $this->__('Country') ?></label>
                        <div class="input-box">
                            <?php echo $this->getCountryHtmlSelect() ?>
                        </div>
                    </div>
                </li>
            </ul>
            <input type="hidden" name="default_billing" value="1" />
            <input type="hidden" name="default_shipping" value="1" />
        </div>
    <?php endif; ?>
        <div class="fieldset">
            <h2 class="legend"><?php echo $this->__('Login Information') ?></h2>
            <ul class="form-list">
                <li class="fields">
                    <div class="field">
                        <label for="password" class="required"><em>*</em><?php echo $this->__('Password') ?></label>
                        <div class="input-box">
                            <input type="password" name="password" id="password" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Password')) ?>" class="input-text required-entry validate-password" />
                        </div>
                    </div>
                    <div class="field">
                        <label for="confirmation" class="required"><em>*</em><?php echo $this->__('Confirm Password') ?></label>
                        <div class="input-box">
                            <input type="password" name="confirmation" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Confirm Password')) ?>" id="confirmation" class="input-text required-entry validate-cpassword" />
                        </div>
                    </div>
                </li>
                <?php echo $this->getChildHtml('form.additional.info'); ?>
            </ul>
        </div>
        <div class="buttons-set">
            <p class="required"><?php echo $this->__('* Required Fields') ?></p>
            <p class="back-link"><a href="<?php echo $this->escapeUrl($this->getBackUrl()) ?>" class="back-link"><small>&laquo; </small><?php echo $this->__('Back') ?></a></p>
            <button type="submit" title="<?php echo Mage::helper('core')->quoteEscape($this->__('Submit')) ?>" class="button"><span><span><?php echo $this->__('Submit') ?></span></span></button>
        </div>
    </form>
    <script type="text/javascript">
    //<![CDATA[
        var dataForm = new VarienForm('form-validate', true);
        <?php if($this->getShowAddressFields()): ?>
        new RegionUpdater('country', 'region', 'region_id', <?php echo $this->helper('directory')->getRegionJson() ?>, undefined, 'zip');
        <?php endif; ?>
    //]]>
    </script>
</div>
