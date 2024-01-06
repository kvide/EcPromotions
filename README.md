# EcPromotions

This is a fork of the Promotions module for [CMS Made Simple](https://www.cmsmadesimple.org/). The module can co-exist
and will not interfere with systems that use the Promotions module.

## Installing

The module requires that the latest version of CMSMSExt (v1.4.5) module as well as
[EcommerceExt](../../../EcommerceExt), [EcOrderMgr](../../../EcOrderMgr), [EcProductMgr](../../../EcProductMgr)
modules are installed on the server.

Download and unzip the latest EcPromotions-x.x.x.xml.zip from [releases](../../releases). Use CMSMS's Module Manager
to upload the unzipped XML file to your server.

The module will only install on servers running CMSMS v2.2.19 on PHP 8.0 or newer. The software may run on older
versions of CMSMS or PHP, but the checks in MinimumCMSVersion() and method.install.php would need to be tweaked.
