<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 10/2/2019
 * Time: 7:33 PM
 */
header("Access-Control-Allow-Origin: *");
header('Content-Type:text/javascript')

?>
function EzCardCartLibrary()
{
    let _ = this;
    this.arProducts = {};

    this.Load = function ()
    {
        _.LoadProducts(_, function(app) {
            app.RenderEcommerceCart();
        });
    }

    this.Run = function ()
    {

    }

    this.RenderEcommerceCart = function()
    {
        for(let currProductIndex in _.arProducts)
        {
            let objProduct = _.arProducts[currProductIndex];
            let node = document.createElement("div");
            node.classList.add("shoppingCartButton");
            let textnode = document.createTextNode("$" + objProduct.value + " - " + objProduct.title);
            node.appendChild(textnode);
            document.getElementById("ezCardEcommerceCart").appendChild(node);
        }

        let styleNode = document.createElement("style");
        styleNode.type = "text/css";

        let styleCssText = `.shoppingCartButton { padding:10px 15px;background:#eee;border-radius:5px;margin-bottom: 5px; border: 1px solid #bbb; }`;

        if(!!(window.attachEvent && !window.opera))
        {
            styleNode.styleSheet.cssText = styleCssText;
        }
        else
        {
            let styleText = document.createTextNode(styleCssText);
            styleNode.appendChild(styleText);
        }

        document.getElementsByTagName('head')[0].appendChild(styleNode);
    }

    this.LoadProducts = function(self,callback)
    {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                self.arProducts = JSON.parse(this.responseText);

                if (typeof callback === "function")
                {
                    callback(self);
                }
            }
        };

        xhttp.open("GET", "<?php echo getFullUrl(); ?>/api/v1/products/get-products-for-cart-cdn", true);
        xhttp.send();
    }
}

let EzCardCart = new EzCardCartLibrary();
EzCardCart.Load();
<?php if (!empty($app->objHttpRequest->Data->Params["inline"]) && $app->objHttpRequest->Data->Params["inline"] === "true") { ?>
EzCardCart.Run();
<?php } ?>
