<?php

function displayIconForConnection($value) : string
{
    if (filter_var($value->value, FILTER_VALIDATE_EMAIL))
    {
        return "https://drive.google.com/thumbnail?id=138vsvcEawA9TXGQ9jnnMUPh1CDmKaMB9";
    }
    elseif (filter_var($value->value, FILTER_VALIDATE_URL))
    {
        return "https://drive.google.com/thumbnail?id=11sWkHI0zc3GL-TmX4l5cDyC30WTXd6pi";
    }
    elseif (isInteger($value->value) && strlen($value->value) < 20)
    {
        switch($value->type)
        {
            case "sms": return "https://drive.google.com/thumbnail?id=122tinmnh6myn8I7W690S9GSzMsQDM8vp";
            default: return "https://drive.google.com/thumbnail?id=12DVkZ4LqZLRBikI6K_3OTiyrzJ8Uve9N";
        }
    }

    return "https://drive.google.com/thumbnail?id=139i3a1cv_hI080lt3W28x-XIjx69R7mW";
}

function renderActionForConnection($value) : ?string
{
    if (filter_var($value->value, FILTER_VALIDATE_EMAIL))
    {
        return "mailto:" . $value->value;
    }
    elseif (filter_var($value->value, FILTER_VALIDATE_URL))
    {
        return $value->value;
    }
    elseif (isInteger($value->value) && strlen($value->value) < 20)
    {
        switch($value->type)
        {
            case "sms": return "sms:" . $value->value;
            default: return "tel:" . $value->value;
        }
    }

    return $value->value;
}

function displayCustomCardLinkIcon($platformId) : ?string
{
    switch($platformId)
    {
        case "872a586c-dcb7-11ea-b088-42010a522005":
            return "https://app.ezcardmedia.com/images/users/1002/70f11a2dae55f0d1ca492ee4013da8ad78292367.png";
        case "ad15c53c-f778-11ea-8b4c-42010a522007":
            return "https://app.ezcardmedia.com/images/users/73a0d552-57e9-11ea-b088-42010a522005/4927af35d25bff1cc58ce9ca82e994edab13556d.png";
        default:
            return "https://drive.google.com/thumbnail?id=11KaePciz7FwcBEe3HWabgxR8ieP_I0Mv";
    }
}

function displayCustomCardLinkIconFaded($platformId) : ?string
{
    switch($platformId)
    {
        case "872a586c-dcb7-11ea-b088-42010a522005":
            return "https://app.ezcardmedia.com/images/users/1002/5491363fd13afcf6d6b5dfa0beebdbb442609264.png";
        case "ad15c53c-f778-11ea-8b4c-42010a522007":
            return "https://app.ezcardmedia.com/images/users/73a0d552-57e9-11ea-b088-42010a522005/f496d2096feb60a82665b34c14fa6cb23b432f6b.png";
        default:
            return "https://drive.google.com/thumbnail?id=11S_AR6VqCf_tOU6xiP2IJOC7132_sUiU";
    }
}

$directoryId = getGuid();

?>
<style data-name="ezcard-visualization-cards-css">
    @import url('https://fonts.googleapis.com/icon?family=Material+Icons');

    .ezcard-visualization-cards {
        font-size: 10pt !important;
        font-family: roboto,sans-serif !important;
        line-height: 15px;
    }

    .ezcard-visualization-cards .cards-container {
        /* Safari / Chrome for Mac  */
        display: -webkit-flex;
        -webkit-flex-wrap: wrap;
        -webkit-justify-content: center;

        display: flex;
        flex-wrap: wrap;
        justify-content: center;

    }

    .ezcard-visualization-cards .cardRecord {
        margin-bottom: 25px;
    }

    .ezcard-visualization-cards .card-content {
        display: block;
        width: 100%;
        margin: 8px;
        padding: 8px;
        box-shadow: rgba(0, 0, 0, 0.4) 0 4px 8px 0;
        transition: box-shadow 500ms cubic-bezier(0.22, 0.84, 0.57, 1.5);
        background-color: white;
        border-radius: 2px;
    }

    .ezcard-visualization-cards .card-content:hover {
        box-shadow: rgba(0, 0, 0, 0.4) 0 8px 16px 0;
        z-index: 10;
    }

    .ezcard-visualization-cards .cards-page-outer {
        display: flex;
        width:100%;
    }

    .ezcard-visualization-cards .cards-page {
        margin: 8px 0 8px 0;
        border-top: solid #E5E5E5 1px;
        border-bottom: solid #E5E5E5 1px;

        display: -webkit-flex;
        -webkit-align-items: center;

        display: flex;
        align-items: center;
        width:100%;
    }

    .ezcard-visualization-cards .cards-page-button {
        margin-left: auto;
        display: flex;

        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .ezcard-visualization-cards .cards-page-button > div {
        cursor: pointer;
        margin: 2px;
    }

    .ezcard-visualization-cards div.cards-page-button-disabled {
        opacity: 0.3;
        cursor: default;
    }

    .ezcard-visualization-cards .cards-hidden {
        display: none !important;
    }

    @media print {

        /* Box-shadow issue in chrome : (https://bugs.chromium.org/p/chromium/issues/detail?id=174583) */
        .ezcard-visualization-cards .card-content {
            border: 1px #eeeeee solid;
        }

    }

    .pointer {
        cursor: pointer;
    }

    body div.universal-float-shield {
        position: fixed;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        width: 100%;
        height: 100%;
        background: url(<?php echo $customPlatformUrl; ?>/website/images/LoadingIcon2.gif) no-repeat left 50% center / auto 35px, rgba(255,255,255,.4) ;
        z-index:11000;
        overflow-y: auto;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .ezcard-visualization-cards .container {
        padding: 0 10px!important;
        text-align: left;
        min-height:80px;
    }
    .ezcard-visualization-cards .containerc {
        padding: 10px 15px;
        text-align: left;
    }
    .ezcard-visualization-cards div.wrap{
        word-wrap:break-word;text-align:left;
    }
    .ezcard-visualization-cards .card-label{
        cursor: pointer;
        padding-bottom: 0;
        width:100%;
    }
    .ezcard-visualization-cards img {
        max-width:100%; max-height: 100%;
    }
    .ezcard-visualization-cards .card-label-image{
        display:block;
        max-width: 90%; max-height: 200px; width:inherit!important;
        margin-left:auto; margin-right:auto;
    }
    .ezcard-visualization-cards .description {
        margin-top:8px;
        padding:0 10px 10px 10px!important;
        text-align:justify;
        font-family: 'roboto', sans-serif;
        color: #000;
    }
    .ezcard-visualization-cards .centered-and-cropped {
        object-fit: cover;
    }
    .ezcard-visualization-cards h1 {
        font-family: 'Oswald', sans-serif;
        font-size: 18px;
        font-weight: 100;
        color: black;
        text-transform: uppercase;
    }
    .ezcard-visualization-cards .moviecat {
        display:none;
        height:auto;
        color:#000;
        margin:5px;
    }
    .ezcard-visualization-cards div[data-categories="Movie"][data-category="movie"] {
        display:inline-block!important;
    }
    .ezcard-visualization-cards div[data-categories="Animated"][data-category="animated"] {
        display:inline-block!important;
    }

    .ezcard-visualization-cards .person{
        max-width:150px; max-height:120px; width:inherit !important;
        display:block;
        margin-right: 10px; float:left;
    }
    hr {
        background-color: #77717F;
        height: 1px;
        border: 0;
    }
    .ezcard-visualization-cards .datas {
        display:block;
        color:#000;
        text-align:justify;
        padding: 0 10px!important;
    }
    .ezcard-visualization-cards .specdatas {
        margin-top:5px;

        font-family: 'Oswald', sans-serif;
        font-size:medium;
    }
    .ezcard-visualization-cards p[data-actors=""] {
        display:none;
    }
    .ezcard-visualization-cards p[data-director=""] {
        display:none;
    }
    .ezcard-visualization-cards .center {
        display:block;margin-left:auto;margin-right:auto;
    }

    .ezcard-visualization-cards .cards-options-outer,
    .ezcard-visualization-cards .cards-options-body,
    .ezcard-visualization-cards .cards-options {
        display: flex;
        width: 100%;
    }

    .ezcard-visualization-cards .cards-options-body > div {
        display:block;
        width: 100%;
    }

    .ezcard-visualization-cards .cards-options-outer .cards-options > div {
        background: #ededed;
        padding: 7px 14px 9px;
        margin-top: 7px;
        cursor: pointer;
        width: calc(50% - 4px);
        font-size: 12px;
    }

    .ezcard-visualization-cards .cards-options-outer .cards-options > div:last-child {
        margin-left: 8px;
    }

    .ezcard-visualization-cards .addNewEntityButton,
    .ezcard-visualization-cards .editEntityButton,
    .ezcard-visualization-cards .filterEntityButton,
    .ezcard-visualization-cards .deleteEntityButton {
        display:inline-block;
        width:15px;
        height:15px;
        position: relative;
    }

    .ezcard-visualization-cards .addNewEntityButton::before {
        display: block;
        width: 15px;
        height: 15px;
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxdJREFUeNqcVL9vHEUYfTM7u3eXvcN3/nFxsM6XRAgJyyYyChISdBQgxWAEFDRuESkSqqQCKfwDNOmTIkqqSCQGCxASDVUEtowiEKGwZNnyjwCOfdi3vt2dGd7s3tkmFg0jfVrtvO+9efN9367ApXs4tiwmAt+7UAq8874nm0LAS1KzEsV6oZPob5jwANzMc232EP8Ssrb/RMH/tN5XvFgrFwoONl1ISQGrDZ60Y2xuRzfbneQzii0fF7I4PdRX/HJ0oDzuKQ8poXo5QKWgMni9tY92rKFoRGuN1b/21ij4FokLDpddJzWKzJ2pV8YFRbT0EFmBD19p4M4H41m8WPWxxz2HCc9Dc6jy7MlqadYZyIVoLSwG15qD5TEj+cokUAy+gnPmruRCMtyeYLgcwxo1BsKRShhcpxiZQowNV0sfSZKsoEBPSJEAcVg+Eq2n4A5zjiRxxThVPTElpHhdlQpquhYWgphVjUjNuCTt0WxyROjPSOP3zZh3YI5OEbANo6HCQBigXPTfU2GgXnadafaX8Orpflie6IzGFHm+FhwIXRirY/AkW+CqSqF4P8Gdn1ax5a7tiUkVKNnsGIuxehlXXmvgv9bM+RHMHN0wGt8urmL579gJDzl9D/9rifzi+WAKlWiz4ktxbmkrwu2fN7GTWETasrgSb5ztw8RgMaN9/dsf+GVHk8caGYO4k2Bb5wPItaU4ZPO+wNSjx3tYfNzGo21aNbnR8N3nDoRuPFjB3SViHolxAqQMYXtfxEPZ7qRzrSi2Rdd5N+6WpXfD7BkEvUSukpJ5x9wZDncjbnJ3FJqV1tof17ejW26jE6dAQr+peyZ50kFxbb7HjvEbOQxjfiD6lRtItNrJJ2tP2hsZ4JKdEEWDwzGCJ7pCPTzNBHcp9DHhWODy/bwHQrwUFv17u6ltZNPNA14YruDUM4UM/3WthY3dOP9t5G5ajPcJfZf3sCvU/QOchRSfw5PTbiihbbcpzpLMi+uuq833FLzKmO9R1VOjsUTwHaT6TQjzNt8n6WwoQ7TeouhD4nN8/8JV7Sj1HwEGAGXUUvW2OWvwAAAAAElFTkSuQmCC) no-repeat center center / 100%;
        position: absolute;
        content: " ";
        top: 3px;
        left: -2px;
    }

    .ezcard-visualization-cards .filterEntityButton::before {
        display:block;
        width:15px;
        height:15px;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAYAAADE6YVjAAAACXBIWXMAAAsTAAALEwEAmpwYAAAF0WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDUgNzkuMTYzNDk5LCAyMDE4LzA4LzEzLTE2OjQwOjIyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDIwLTA3LTE0VDAwOjA3OjQ1LTA1OjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDIwLTA3LTE0VDAwOjA3OjQ1LTA1OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyMC0wNy0xNFQwMDowNzo0NS0wNTowMCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpkZjU2MzAxNi1jY2EwLTE3NGMtYWE4MS05N2I3NzU5NGM0ZTAiIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDpiZWY0Yjk1Ni1mOGQ3LTkzNGEtYTYzYi03MmI3ZWZjNDhkYmUiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo1NzE2YzJjNS05MzgwLTIwNDAtYjg3MS0wNDI0M2IxMGI0NDgiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBwaG90b3Nob3A6Q29sb3JNb2RlPSIzIj4gPHhtcE1NOkhpc3Rvcnk+IDxyZGY6U2VxPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0iY3JlYXRlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDo1NzE2YzJjNS05MzgwLTIwNDAtYjg3MS0wNDI0M2IxMGI0NDgiIHN0RXZ0OndoZW49IjIwMjAtMDctMTRUMDA6MDc6NDUtMDU6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE5IChXaW5kb3dzKSIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6ZGY1NjMwMTYtY2NhMC0xNzRjLWFhODEtOTdiNzc1OTRjNGUwIiBzdEV2dDp3aGVuPSIyMDIwLTA3LTE0VDAwOjA3OjQ1LTA1OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoV2luZG93cykiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+GIsaBwAAAu9JREFUSMet1k1onFUUBuDn3swkk4RYG5Tiz0IUqm66UCouBFEUF3FjXHXThRvtwqLGbSkFW9SFghs3boyIUC1x0WALkkgpEhwitBWMtmAUbRK0STRNZjI/3+dipiGTzJ/guz3n3Pc733nPe29YOaIdcngRT+MR3IV+FLCA7zGFMyi2OiS0IXkDr+NenfE73sd73ZI8JBhXcTAtEWJnhjQh9CIjL3UYc9vjmR35j2JaYkguS39FWkk7s2QCaYZS+aDgOzyF2WYk9+GiVC5ZpXfkBQOjH0hW/+jIEffcY2PiqNLkafF2Q4KLeBjzO0nO1wct5ChfucChYZm793Xxu8rKVy4IuQbBnMeDcOuPj2F/bUqEfpJri9bHR3WDm+OjkmuLQn+tvo799XNF9OH4zsKeYUrTZ5V//bYtQfm3GaWps3qGm4aPoy9iFEO7wllCkY3TL7cl2fjildqGZJuGhzAaMdJyoHsp53+wOftx0/jmpc+UZy7p2dv2O0YiDrSWJjFL4cxr0rTSGEuqCp8fFTNNFqERByJayyclDlG5umrjq2MNocLXb6nM/SXuqeW1wb54S7atl4A4wOa5tyVrC7Um1m8onjspDjSoqaX/xXbGtuU9GUKFtLBSIymsSgtlIduVwosRS523DdlA7KnJ+44HhP5ekq5IliIud0UiFYfvB6X8J2xUhUxXJJcjJjumBWLuNpVfZvz94ZP+OXVYSKq1Ne6MybByRB/+bLqQ21HNSHJVuWffZOW64jefitltxtQca7gzYhMnOnWSFCrSQmrg+XdlHz8kLelmJiewuf3S+mnLJHcZFEkPgy99JC2tK3z5jnDzek2+rSX8804XhudayjkQAsmNRWm5KPfMq9JeVFrLtn7erktrHk9getd8sqQrVJd+ZDCnPDUhhJamuFa/Gee37XMDZvEY8s3spXp1SjU/QWG5VQf5ev3sDtPYhbl64lj9FbLVc7q0IF1eFgabvlbG6nVz/+VJ9L+9u/4Fcjr9jlRe1d0AAAAASUVORK5CYII=') no-repeat center center / 100%;
        position: absolute;
        content: " ";
        top: 3px;
        left: -3px;
    }

    .ezcard-visualization-cards .editEntityButton::before {
        display:block;
        width:22px;
        height:22px;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH4gkUCx4h0U5y8AAAA6xJREFUWMPN2EtoXFUcx/FPbiKK1kqbWkurWVjFirHWB1WKSrFudGMUhSr42mjx0Soo+NiIK1EUFYtmo+BCUBEqFio+sNGSSkVahRjtFBu01UVaMal9QJ3Exf9MejOZSWbiTegPhuGeOffO9/7P//wfp0UTunATaMUSXIaVuBiLcAZGcRh/og878GO6Hil1Nf5fLQ3CwOlYhTtwPc5LY/WeUYEcwFZ8mECPwVSQk4IlqFMSyHrcgDnNWDmnIXyG17Ed5cngaoLlrLQIT+J+zJsmULUG0Y1XcbCe9SaA5aAux8tYrYElb1Ij2JJeur8W3Lg/zEGtSm/VWTBQtb7Dg9hZDTcGVmWpdwuG+ls4/TkmWn8H7sXPebisatJivFIw1F7ciRvxvtitea3ES2jPD2Y5a7WJNV9dINQQhp2Ia+vxXg24m/EossrKZbklXIP7CoQq4S58irewQuzIx2vAZViHayoDre1rEVH7RSwvCGqPcOrP0Yvz8RC+x6/4Gufi0tw9c3AaNrevVa742LWKW8I9eABfpesjeA7f4M1kuQN4VnL4nG7CVRUTZrjN9CN6XahS19guq4ZbLpZypOr++bilAtYhUk7hUBVVwfUIn3tbFADVWoOFmYhbHTMFVaUjeAcLRN6tlVGWojPDlaJKmDGo3M5fghdwwSTPm4sVbVj2P6BKYvc1CrVR8qFJlGFZJqL9yQJV0eJMxLCTCQrmZk1MrqgSPGcKCkYz/NOkpZpx9OlAwXAb9jc4+bcEtXWGoWBfhp8amHgMv+N4vQkFQpXRn4nEOtVy7sNreATXVYEUCUWUSrvaRFk7YPLi8Ci+TICPiTy3LQ9XEBTsRl/Fx3qmADsslnG7SCMb0vi2gqEkAxxsS2//Ee4W6aCWDjnhX73pe4OoDgYKhBrEx0Q5Dd+KZvT2OjcMG+/4vaKoewpnSX5XgD7BrjzYUbwhisUFNW7oEP3A/PSZl6zbKZriIrRf1GrHS12p7MgdljyPpxXf4E6lMp4R5b1SV+qSUrAsi7Z9yyxDET7enWOZ0FcO4gnRIc+WeoSvDuUHx8ByKaZfJOnZgOsRbdveKobxFsv9sBP3YLOJDUMR+hcfiD523NFARVMdQ7WL7nkdFhYE9YdIb90YqtcfNHJw14qr8bDo+6Z7TnZAxKmNYkVGmj64q2O9U3EFbhUdzlIRy+oVm2URmHfjC2zCD1KcmkpNxasE2YKzcYk4IL5I5MozRXo7JEqkXxJIH/7CaDOHw/8BwN0RTQ5fFN4AAAAldEVYdGRhdGU6Y3JlYXRlADIwMTgtMDktMjBUMTE6MzA6MzMtMDQ6MDD2YkMTAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE4LTA5LTIwVDExOjMwOjMzLTA0OjAwhz/7rwAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAAASUVORK5CYII=') no-repeat center center / 100%;
        position: absolute;
        content: " ";
        top: 0;
        left: -3px;
    }

    .ezcard-visualization-cards .containera {
        border: 5px solid rgb(239,143,33);
        border-radius: .5em;
        padding: 1px;
        display: flex;
    }
    .ezcard-visualization-cards .col1 {
        width: 22%;
        height: 80px;
        padding: 2px;
    }
    .ezcard-visualization-cards .col2 {
        width: 58%;
        height: 80px;overflow-wrap: break-word;
        padding: 2px;
    }
    .ezcard-visualization-cards .col21 {
        width: 73%;
        height: 80px;overflow-wrap: break-word;
        padding: 2px;
    }
    .ezcard-visualization-cards .col3 {
        width: 20%;
        height: 75px;
        padding: 2px;
    }
    .ezcard-visualization-cards .containerb {
        width: 95%;
        height: 65px;
        padding: 1px;
        display: flex;
    }
    .ezcard-visualization-cards .col4 {
        width: 16.66%;
        height: 60px;
        text-align: center;
        padding: 2px;
    }
    .ezcard-visualization-cards .col5 {
        width: 20%;
        height: 60px;
        padding: 2px;
    }
    .ezcard-visualization-cards .col6 {
        width: 20%;
        height: 60px;
        padding: 2px;
    }
    .ezcard-visualization-cards .col7 {
        width: 20%;
        height: 60px;
        padding: 2px;
    }
    .ezcard-visualization-cards .col8 {
        width: 20%;
        height: 60px;
        padding: 2px;
    }
    /*** Awesome Table ***/
    .ezcard-visualization-cards .card-content {
        background-color: #fff!important;
        padding: 0!important;
    }
    .ezcard-visualization-cards .card-content:hover {
        box-shadow: rgba(0, 0, 0, 1) 0 8px 16px 0!important;
    }
    .ezcard-visualization-cards figure {width:100px;overflow:hidden;margin-top:-10px;} /* loader progress bar */
    .ezcard-visualization-cards .loader {
        background-color:#F0141E;
    }

    .ezcard-visualization-cards .at-svg-icon {
        flex: auto;
    }
    .ezcard-visualization-cards .at-pagination-button {
        width: 24px;
        height: 24px;
    }

    /** Labels of filters **/
    .google-visualization-controls-label {
        color:#333;
    }

    /** StringFilter **/
    .google-visualization-controls-stringfilter INPUT {
        border:1px solid #d9d9d9!important;
        color:#222;
    }
    .google-visualization-controls-stringfilter INPUT:hover {
        border:1px solid #b9b9b9;
        border-top:1px solid #a0a0a0;
    }
    .google-visualization-controls-stringfilter INPUT:focus {
        border:1px solid #4d90fe;
    }

    /* CategoryFilter & csvFilter hovered item in the dropDown */
    .ezcard-visualization-cards .charts-menuitem-highlight {
        background-color:#44444F!important;
        border-color:#44444F!important;
        color:#FFF!important;
    }

    .ezcard-visualization-cards .google-visualization-controls-label {
        font-weight: 500!important;
    }

    .error-validation {
        border:2px solid #ff0000;
        box-shadow: #ff0000 0 0 5px;
    }

    .error-text {
        color: red;
        padding: 0px 10px 10px;
    }

    .error-message-text {
        color: red;
        padding: 7px 14px 0px;
    }

    .error-text .fa {
        position: relative;
        top: 2px;
    }

    .cards-options-add-record-success {
        padding: 10px 25px;
    }
    .cards-options-add-record-success h4 {
        text-align: center;
    }

    @media (max-width: 550px) {
        .ezcard-visualization-cards .mobile {
            margin: 10px auto 10px auto!important;
            float:none;
        }
        .ezcard-visualization-cards .mobile-bis {
            margin : 10px!important;
        }
    }
    @media (min-width: 550px) {
        .ezcard-visualization-cards .mobile {
            margin: 20px!important;
            float:left;
        }
        .ezcard-visualization-cards .mobile-bis {
            margin : 20px!important;
        }
    }
    .ezcard-visualization-cards .cardRecord {min-width:250px;max-width:100%;width:calc(100% / 1);}
</style>
<script type="text/javascript">
    function imgError(image) {
        image.onerror = "";
        image.src = "/_ez/images/users/defaultAvatar.jpg";
        return true;
    }

    function logoError(image) {
        image.onerror = "";
        image.src = "/_ez/images/users/defaultLogo.png";
        return true;
    }
</script>
<?php
$directoryName = $objDirectory->defaults->FindEntityByValue("label", "directory_name")->value ?? "";
$headerImage = $objDirectory->defaults->FindEntityByValue("label", "header_image")->value ?? "";
$headerHtml = $objDirectory->defaults->FindEntityByValue("label", "header_html")->value ?? "";
$footerImage = $objDirectory->defaults->FindEntityByValue("label", "footer_image")->value ?? "";
$mainColor = $objDirectory->defaults->FindEntityByValue("label", "main_color")->value ?? "";
$mainColor = (!empty($mainColor)) ? $mainColor : "8e8e8e";

$pageMax = (integer) ($objDirectory->defaults->FindEntityByValue("label", "page_max")->value ?? 25);
$mainEzcardId = $cardId ?? "";
$pageIndex = 0;

/** @var \App\Utilities\Excell\ExcellCollection $colDirectoryRecord */
$colDirectoryRecord = $objDirectoryRecordResult->Data;
$sortField = $objDirectory->defaults->FindEntityByValue("label", "sort_by")->value ?? "first_name";
$sortOrder = $objDirectory->defaults->FindEntityByValue("label", "sort_order")->value ?? "asc";
$colDirectoryRecord->SortBy($sortField, $sortOrder);
?>
<div id="<?php echo $directoryId; ?>" class="ezcard-visualization-cards">
    <?php if (!empty($headerImage)) { ?>
        <div class="cards-header">
            <img src="<?php echo $headerImage; ?>" style="width:100%;"/>
        </div>
    <?php } ?>
    <div class="cards-header-html">
        <?php echo $headerHtml; ?>
    </div>
    <div class="cards-options-outer" style="display: none;">
        <div class="cards-options">
            <div class="sign-in-to-ez-digital-<?php echo $directoryId; ?>"><span class="pointer addNewEntityButton"></span> Get Connected</div>
            <div class="card-filter-<?php echo $directoryId; ?>"><span class="pointer filterEntityButton"></span> Filter This Directory</div>
        </div>
    </div>
    <div class="cards-options-outer">
        <div class="cards-options-body">
            <div class="cards-options-add-record" style="display: block;padding:0 15px;">
                <h4 style="margin-top: 25px; font-size: 24px;text-align:center;">Get Connected To Dr. Lydie!</h4>
                <p style="line-height:24px;font-size: 17px;">By registering today you will receive an exclusive, limited-time gift valued at $750, just for becoming a part of Dr. Lydie's team.</p>
                <h5 style="text-align: center;font-size: 20px;background: rgb(51, 204, 204);border-radius:10px;padding:15px;margin-bottom:25px;">
                    <span class="fas fa-file-alt" style="font-size:45px;display: block;margin-bottom:10px;"></span>A Dr. Lydie Drafted and Enforced<br>ND NC NC Agreement
                </h5>
                <div style="text-align:center;margin-bottom: 20px;font-size: 25px;">Register below!</div>

                <input class="form-control first-name-<?php echo $directoryId; ?>" name="first_name" type="text" placeholder="First Name" value=""><br>
                <input class="form-control last-name-<?php echo $directoryId; ?>" name="last_name" type="text" placeholder="Last Name" value=""><br>
                <input class="form-control title-<?php echo $directoryId; ?>" name="title" type="text" placeholder="Your Title" value=""><br>
                <input class="form-control company-<?php echo $directoryId; ?>" name="company" type="text" placeholder="Company Name" value=""><br>
                <select class="form-control industry-<?php echo $directoryId; ?>" name="industry">
                    <option>--Select An Industry--</option>
                    <option value="Accounting">Accounting</option>
                    <option value="Airlines/Aviation">Airlines/Aviation</option>
                    <option value="Alternative Dispute Resolution">Alternative Dispute Resolution</option>
                    <option value="Alternative Medicine">Alternative Medicine</option>
                    <option value="Animation">Animation</option>
                    <option value="Apparel/Fashion">Apparel/Fashion</option>
                    <option value="Architecture/Planning">Architecture/Planning</option>
                    <option value="Arts/Crafts">Arts/Crafts</option>
                    <option value="Automotive">Automotive</option>
                    <option value="Aviation/Aerospace">Aviation/Aerospace</option>
                    <option value="Banking/Mortgage">Banking/Mortgage</option>
                    <option value="Biotechnology/Greentech">Biotechnology/Greentech</option>
                    <option value="Broadcast Media">Broadcast Media</option>
                    <option value="Building Materials">Building Materials</option>
                    <option value="Business Supplies/Equipment">Business Supplies/Equipment</option>
                    <option value="Capital Markets/Hedge Fund/Private Equity">Capital Markets/Hedge Fund/Private Equity</option>
                    <option value="Chemicals">Chemicals</option>
                    <option value="Civic/Social Organization">Civic/Social Organization</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Commercial Real Estate">Commercial Real Estate</option>
                    <option value="Computer Games">Computer Games</option>
                    <option value="Computer Hardware">Computer Hardware</option>
                    <option value="Computer Networking">Computer Networking</option>
                    <option value="Computer Software/Engineering">Computer Software/Engineering</option>
                    <option value="Computer/Network Security">Computer/Network Security</option>
                    <option value="Construction">Construction</option>
                    <option value="Consumer Electronics">Consumer Electronics</option>
                    <option value="Consumer Goods">Consumer Goods</option>
                    <option value="Consumer Services">Consumer Services</option>
                    <option value="Cosmetics">Cosmetics</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Defense/Space">Defense/Space</option>
                    <option value="Design">Design</option>
                    <option value="E-Learning">E-Learning</option>
                    <option value="Education Management">Education Management</option>
                    <option value="Electrical/Electronic Manufacturing">Electrical/Electronic Manufacturing</option>
                    <option value="Entertainment/Movie Production">Entertainment/Movie Production</option>
                    <option value="Environmental Services">Environmental Services</option>
                    <option value="Events Services">Events Services</option>
                    <option value="Executive Office">Executive Office</option>
                    <option value="Facilities Services">Facilities Services</option>
                    <option value="Farming">Farming</option>
                    <option value="Financial Services">Financial Services</option>
                    <option value="Fine Art">Fine Art</option>
                    <option value="Fishery">Fishery</option>
                    <option value="Food Production">Food Production</option>
                    <option value="Food/Beverages">Food/Beverages</option>
                    <option value="Fundraising">Fundraising</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Gambling/Casinos">Gambling/Casinos</option>
                    <option value="Glass/Ceramics/Concrete">Glass/Ceramics/Concrete</option>
                    <option value="Government Administration">Government Administration</option>
                    <option value="Government Relations">Government Relations</option>
                    <option value="Graphic Design/Web Design">Graphic Design/Web Design</option>
                    <option value="Health/Fitness">Health/Fitness</option>
                    <option value="Higher Education/Acadamia">Higher Education/Acadamia</option>
                    <option value="Hospital/Health Care">Hospital/Health Care</option>
                    <option value="Hospitality">Hospitality</option>
                    <option value="Human Resources/HR">Human Resources/HR</option>
                    <option value="Import/Export">Import/Export</option>
                    <option value="Individual/Family Services">Individual/Family Services</option>
                    <option value="Industrial Automation">Industrial Automation</option>
                    <option value="Information Services">Information Services</option>
                    <option value="Information Technology/IT">Information Technology/IT</option>
                    <option value="Insurance">Insurance</option>
                    <option value="International Affairs">International Affairs</option>
                    <option value="International Trade/Development">International Trade/Development</option>
                    <option value="Internet">Internet</option>
                    <option value="Internet Marketing">Internet Marketing</option>
                    <option value="Investment Banking/Venture">Investment Banking/Venture</option>
                    <option value="Investment Management/Hedge Fund/Private Equity">Investment Management/Hedge Fund/Private Equity</option>
                    <option value="Judiciary">Judiciary</option>
                    <option value="Law Enforcement">Law Enforcement</option>
                    <option value="Law Practice/Law Firms">Law Practice/Law Firms</option>
                    <option value="Legal Services">Legal Services</option>
                    <option value="Legislative Office">Legislative Office</option>
                    <option value="Leisure/Travel">Leisure/Travel</option>
                    <option value="Library">Library</option>
                    <option value="Logistics/Procurement">Logistics/Procurement</option>
                    <option value="Luxury Goods/Jewelry">Luxury Goods/Jewelry</option>
                    <option value="Machinery">Machinery</option>
                    <option value="Management Consulting">Management Consulting</option>
                    <option value="Maritime">Maritime</option>
                    <option value="Market Research">Market Research</option>
                    <option value="Marketing/Advertising/Sales">Marketing/Advertising/Sales</option>
                    <option value="Mechanical or Industrial Engineering">Mechanical or Industrial Engineering</option>
                    <option value="Media Production">Media Production</option>
                    <option value="Medical Equipment">Medical Equipment</option>
                    <option value="Medical Practice">Medical Practice</option>
                    <option value="Mental Health Care">Mental Health Care</option>
                    <option value="Military Industry">Military Industry</option>
                    <option value="Mining/Metals">Mining/Metals</option>
                    <option value="Motion Pictures/Film">Motion Pictures/Film</option>
                    <option value="Museums/Institutions">Museums/Institutions</option>
                    <option value="Music">Music</option>
                    <option value="Nanotechnology">Nanotechnology</option>
                    <option value="Newspapers/Journalism">Newspapers/Journalism</option>
                    <option value="Non-Profit/Volunteering">Non-Profit/Volunteering</option>
                    <option value="Oil/Energy/Solar/Greentech">Oil/Energy/Solar/Greentech</option>
                    <option value="Online Publishing">Online Publishing</option>
                    <option value="Other Industry">Other Industry</option>
                    <option value="Outsourcing/Offshoring">Outsourcing/Offshoring</option>
                    <option value="Package/Freight Delivery">Package/Freight Delivery</option>
                    <option value="Packaging/Containers">Packaging/Containers</option>
                    <option value="Paper/Forest Products">Paper/Forest Products</option>
                    <option value="Performing Arts">Performing Arts</option>
                    <option value="Pharmaceuticals">Pharmaceuticals</option>
                    <option value="Philanthropy">Philanthropy</option>
                    <option value="Photography">Photography</option>
                    <option value="Plastics">Plastics</option>
                    <option value="Political Organization">Political Organization</option>
                    <option value="Primary/Secondary Education">Primary/Secondary Education</option>
                    <option value="Printing">Printing</option>
                    <option value="Professional Training">Professional Training</option>
                    <option value="Program Development">Program Development</option>
                    <option value="Public Relations/PR">Public Relations/PR</option>
                    <option value="Public Safety">Public Safety</option>
                    <option value="Publishing Industry">Publishing Industry</option>
                    <option value="Railroad Manufacture">Railroad Manufacture</option>
                    <option value="Ranching">Ranching</option>
                    <option value="Real Estate/Mortgage">Real Estate/Mortgage</option>
                    <option value="Recreational Facilities/Services">Recreational Facilities/Services</option>
                    <option value="Religious Institutions">Religious Institutions</option>
                    <option value="Renewables/Environment">Renewables/Environment</option>
                    <option value="Research Industry">Research Industry</option>
                    <option value="Restaurants">Restaurants</option>
                    <option value="Retail Industry">Retail Industry</option>
                    <option value="Security/Investigations">Security/Investigations</option>
                    <option value="Semiconductors">Semiconductors</option>
                    <option value="Shipbuilding">Shipbuilding</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Sporting Goods">Sporting Goods</option>
                    <option value="Sports">Sports</option>
                    <option value="Staffing/Recruiting">Staffing/Recruiting</option>
                    <option value="Supermarkets">Supermarkets</option>
                    <option value="Telecommunications">Telecommunications</option>
                    <option value="Textiles">Textiles</option>
                    <option value="Think Tanks">Think Tanks</option>
                    <option value="Tobacco">Tobacco</option>
                    <option value="Translation/Localization">Translation/Localization</option>
                    <option value="Transportation">Transportation</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Venture Capital/VC">Venture Capital/VC</option>
                    <option value="Veterinary">Veterinary</option>
                    <option value="Warehousing">Warehousing</option>
                    <option value="Wholesale">Wholesale</option>
                    <option value="Wine/Spirits">Wine/Spirits</option>
                    <option value="Wireless">Wireless</option>
                    <option value="Web Design">Web Design</option>
                    <option value="Writing/Editing">Writing/Editing</option>
                </select>
                <br>
                <input class="form-control email-<?php echo $directoryId; ?>" name="email" type="email" placeholder="Email" value=""><br>
                <input class="form-control phone-<?php echo $directoryId; ?>" name="phone" type="phone" placeholder="Mobile Phone" value=""><br>
                <div style="text-align:center;margin-bottom: 15px;">The username and password is for future Dr. Lydie services</div>
                <input class="form-control username-new-<?php echo $directoryId; ?>" style="margin-top:25px;" name="username" type="username" id="" placeholder="Username" value=""><br>
                <input class="form-control password-new-<?php echo $directoryId; ?>" name="password" type="password" placeholder="Password" value=""><br>
                <input class="form-control password-new-<?php echo $directoryId; ?>-2" name="password2" type="password" placeholder="Retype Password" value=""><br>
                <div style="text-align:center;margin-bottom: 15px;">Privacy: We won't share any of your information without your explicit approval!</div>
                <div class="errors-1-<?php echo $directoryId; ?>"></div>
                <button class="btn ajax-submit-button submitSendEmail" type="submit" name="submitSendEmail1" id="submitSendEmail1-<?php echo $directoryId; ?>" style="margin-bottom:15px;width:100%;background: rgb(51, 204, 204);color:#000;">Register</button>
            </div>
            <div class="cards-options-add-record-error" style="display: none;">
                <h4 style="margin-top: 25px; font-size: 24px;text-align:center;">Get Connected To Dr. Lydie!</h4>
                <div class="error-message-text">Unfortunately, there was an error registering you for Dr Lydie's account.<br><br><div class="cards-options-add-record-error-text"></div><br><br><b>We apologies for the inconvenience.</b></div>
                <div style="text-align:center;margin: 15px 0 15px;"><a class="pointer sign-in-to-ez-digital-<?php echo $directoryId; ?>" data-parent="cards-options-add-record-error" style="text-decoration: underline;">Back To Login/Register</a></div>
            </div>
            <div class="cards-options-add-record-success" style="display: none;">
                <h4 style="margin-top: 25px; font-size: 24px;text-align:center;">Get Connected To Dr. Lydie!</h4>
                <div style="text-align: center;font-size: 20px;background: rgb(51, 204, 204);border-radius:10px;padding:15px;margin-bottom:10px;margin-top:15px;"><b>SUCCESS!</b></div><br><div style="text-align: center;"><b>Enjoy your free service resource for registering!<br><br>We've also created your Dr. Lydie account for future services!</b></div>
            </div>
            <div class="cards-options-add-via-signin" style="display: none;padding:0 15px;">
                <h4 style="margin-top: 25px; font-size: 24px;text-align:center;">Get Connected To Dr. Lydie!</h4>
                <div style="text-align:center;margin-bottom: 15px;">Login to your existing <?php echo $customPlatformName; ?> Account</div>
                <div style="text-align:center;margin-top: 5px;margin-bottom: 5px;font-weight: bold">-- OR --</div>
                <div style="text-align:center;margin: 15px 0 15px;"><a class="pointer create-ez-digital-account" style="padding:5px 10px; color:#fff; background: #888; border-radius: 5px;">Create New Account Here</a></div>
                <input class="form-control username-<?php echo $directoryId; ?>" style="margin-top:25px;" name="username" type="username" placeholder="Username" value=""><br>
                <input class="form-control password-<?php echo $directoryId; ?>" name="password" type="password" placeholder="Password" value=""><br>
                <div class="errors-2-<?php echo $directoryId; ?>"></div>
                <button class="btn ajax-submit-button submitSendEmail" type="submit" name="submitSendEmail2" id="submitSendEmail2-<?php echo $directoryId; ?>" style="width:100%;background-color: #36498f;color:#fff;margin-bottom:15px;">Login/Register</button>
            </div>
            <div class="cards-options-add-via-signin-error" style="display: none;">
                <h4 style="margin-top: 15px; font-size: 19px;">Request Directory Membership</h4>
                <div class="error-message-text">Unfortunately, there was an error registering you for this directory.<br><br><div class="cards-options-add-via-signing-error-text"></div><br><br><b>We apologies for the inconvenience.</b></div>
                <div style="text-align:center;margin: 15px 0 15px;"><a class="pointer sign-in-to-ez-digital" data-parent="cards-options-add-via-signin-error" style="text-decoration: underline;">Back To Login/Register</a></div>
            </div>
            <div class="cards-options-add-via-signin-success" style="display: none;">
                <h4 style="margin-top: 15px; font-size: 19px;">Request Directory Membership</h4>
                <div><b>SUCCESS!</b><br><br><b>We've registered you for this directory and notified the owner. They will review your submission soon.</b></div>
                <div style="text-align:center;margin: 15px 0 15px;"><a class="pointer close-parent-dialog" data-parent="cards-options-add-via-signin-success" style="text-decoration: underline;">Continue!</a></div>
            </div>
            <div class="cards-options-filter" style="display: none;">
                <h4 style="margin-top: 15px; font-size: 19px;">Filter Directory Members</h4>
                <input id="filter_first_name-<?php echo $directoryId; ?>" class="form-control" placeholder="First Name" style="margin-bottom:10px;" />
                <input id="filter_last_name-<?php echo $directoryId; ?>" class="form-control" placeholder="Last Name" style="margin-bottom:10px;" />
                <input id="filter_origanization-<?php echo $directoryId; ?>" class="form-control" placeholder="Organization Name" style="margin-bottom:10px;" />
            </div>
        </div>
    </div>
    <div class="cards-page-outer" style="display: none;">
        <div class="cards-page">
            <div class="cards-pageNumber"><span id="currentPageStart-<?php echo $directoryId; ?>">1</span> - <span id="currentPageEnd-<?php echo $directoryId; ?>"><?php echo $pageMax; ?></span> / <span id="totalCardRecords-<?php echo $directoryId; ?>"><?php echo $objDirectoryRecordResult->Data->Count(); ?></span></div>
            <div class="cards-page-button">
                <select class="pagination-max-count" style="border:0;">
                    <option<?php echo (($pageMax === 5) ? " selected" : "") ?>>5</option>
                    <option<?php echo (($pageMax === 15) ? " selected" : ""); ?>>15</option>
                    <option<?php echo (($pageMax === 25) ? " selected" : ""); ?>>25</option>
                    <option<?php echo (($pageMax === 50) ? " selected" : ""); ?>>50</option>
                    <option<?php echo (($pageMax === 100) ? " selected" : ""); ?>>100</option>
                </select>
                <div id="prev-card-page-<?php echo $directoryId; ?>" class="pointer at-pagination-button">
                    <svg class="at-svg-icon" viewBox="0 0 24 24"><g><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"></path></g></svg>
                </div>
                <div id="next-card-page-<?php echo $directoryId; ?>" class="pointer at-pagination-button">
                    <svg class="at-svg-icon" viewBox="0 0 24 24"><g><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"></path></g></svg>
                </div>
            </div>
        </div>
    </div>
    <div class="cards-container" style="display: none;">
        <?php
        foreach($colDirectoryRecord as $currRecord)
        {
            ?>
            <?php

            $orgLogo = "/_ez/images/users/defaultLogo.png";

            if (!empty($currRecord->custom))
            {
                $ezcardId = $currRecord->custom->FindEntityByValue("label", "ezcard_id")->value ?? "";
                $userAbout = $currRecord->custom->FindEntityByValue("label", "about")->value ?? "";
                $address_1 = $currRecord->custom->FindEntityByValue("label", "address_1")->value ?? "";
                $address_2 = $currRecord->custom->FindEntityByValue("label", "address_2")->value ?? "";
                $city = $currRecord->custom->FindEntityByValue("label", "city")->value ?? "";
                $state = $currRecord->custom->FindEntityByValue("label", "state")->value ?? "";
                $country = $currRecord->custom->FindEntityByValue("label", "country")->value ?? "";
                $zip = $currRecord->custom->FindEntityByValue("label", "zip")->value ?? "";
                $orgName = $currRecord->custom->FindEntityByValue("label", "org_name")->value ?? "";
                $orgAbout = $currRecord->custom->FindEntityByValue("label", "org_about")->value ?? "";
                $orgLogo = $currRecord->custom->FindEntityByValue("label", "logo")->value ?? "/_ez/images/users/defaultLogo.png";
                $tagLine = $currRecord->custom->FindEntityByValue("label", "tag")->value ?? "";

                $connection1 = $currRecord->custom->FindEntityByValue("label", "connection_1") ?? "";
                $connection2 = $currRecord->custom->FindEntityByValue("label", "connection_2") ?? "";
                $connection3 = $currRecord->custom->FindEntityByValue("label", "connection_3") ?? "";
                $connection4 = $currRecord->custom->FindEntityByValue("label", "connection_4") ?? "";
            }

            $hexColor = (!empty($currRecord->hex_color) ? $currRecord->hex_color : $mainColor);
            ?>
            <div class="cardRecord" data-card-index="<?php echo $pageIndex; ?>" data-card-total="<?php echo $pageMax; ?>" data-filter-allow="true" data-filter-first-name="<?php echo $currRecord->first_name; ?>" data-filter-last-name="<?php echo $currRecord->last_name; ?>" data-filter-organization="<?php echo $orgName; ?>">
                <div class="card-label" data-toggle-visibility="controller">
                    <div style="width: 100%;
                        border: 5px solid #<?php echo $hexColor; ?>;
                        border-radius: .5em;
                        padding: 1px;
                        display: flex;">
                        <div class="col1">
                            <img src="<?php echo (!empty($currRecord->profile_image_url) ? $currRecord->profile_image_url : "/_ez/images/users/defaultAvatar.jpg"); ?>" width="82px" height="82px" style="border-radius:10%"class="centered-and-cropped" onerror="imgError(this);" />
                        </div>
                        <div class="col2" >
                            <?php if (!empty($currRecord->first_name . $currRecord->last_name)) { ?>
                                <div><b style="font-weight:bold;"><?php echo $currRecord->first_name; ?> <?php echo $currRecord->last_name; ?></b></div>
                            <?php } ?>
                            <?php if (!empty($orgName) || !empty($currRecord->title)) { ?>
                                <?php if (!empty($currRecord->title)) { ?>
                                    <div><a style="color: #<?php echo $hexColor; ?>"><b><?php echo $currRecord->title; ?></b></br></a></div>
                                <?php } ?>
                                <?php if (!empty($orgName)) { ?>
                                    <div style="font-style: italic;color: #888888;"><?php echo $orgName; ?></div>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!empty($currRecord->mobile_phone)) { ?>
                                <div><?php echo formatAsPhoneIfApplicable($currRecord->mobile_phone); ?></div>
                            <?php } ?>
                            <?php if (!empty($city) || !empty($state)) { ?>
                                <div><?php echo $city; ?> <?php echo $state; ?></div>
                            <?php } ?>
                        </div>
                        <div class="col3" style="margin-top:4px;"><img src="<?php echo $orgLogo; ?>" onerror="logoError(this);"  /></div>
                    </div>
                </div>
                <div class="card-details" style="display:none;">
                    <div class="containerc">
                        <?php if (!empty($tagLine)) { ?>
                            <a style="color: #<?php echo $hexColor; ?>">
                                <b><?php echo $tagLine; ?></b></a><br>
                        <?php } ?>
                        <?php if (!empty($address_1)) { ?>
                            <?php echo $address_1; ?><br>
                        <?php } ?>
                        <?php if (!empty($address_2)) { ?>
                            <?php echo $address_2; ?><br>
                        <?php } ?>
                        <?php if (!empty($city)) { ?>
                            <?php echo $city; ?>,
                        <?php } ?>
                        <?php if (!empty($state)) { ?>
                            <?php echo $state; ?><br>
                        <?php } ?>
                        <?php if (!empty($zip)) { ?>
                            <?php echo $zip; ?>
                        <?php } ?>
                        <?php if (!empty($country)) { ?>
                            <?php echo $country; ?><br>
                        <?php } ?>
                        <?php echo (!empty($userAbout) ? "<hr>" : "")  ?>
                        <?php echo $userAbout; echo ((!empty($orgAbout) && !empty($userAbout)) ? "<hr>" : "")  ?>
                        <?php echo $orgAbout; ?>
                    </div>
                    <div style="width: 100%;height:65px;padding:1px;display:flex;background-color: #<?php echo $hexColor; ?>">
                        <div class="col4">
                            <?php if (!empty($ezcardId)) { ?>
                                <a href="<?php echo $customPlatformUrl; ?>/<?php echo $ezcardId; ?>" target="_blank">
                                    <img src="<?php echo displayCustomCardLinkIcon($platformId); ?>" alt="EZcard" class="center">
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $customPlatformUrl; ?>/<?php echo $mainEzcardId; ?>" target="_blank">
                                    <img src="<?php echo displayCustomCardLinkIconFaded($platformId); ?>" alt="EZcard" class="center">
                                </a>
                            <?php } ?>
                        </div>
                        <div class="col4">
                            <a href="<?php echo renderActionForConnection($connection1); ?>"target="_blank">
                                <img src="<?php echo displayIconForConnection($connection1); ?>" class="center" />
                            </a>
                        </div>
                        <div class="col4">
                            <a href="<?php echo renderActionForConnection($connection2); ?>" target="_blank">
                                <img src="<?php echo displayIconForConnection($connection2); ?>" class="center" />
                            </a>
                        </div>
                        <div class="col4">
                            <a href="<?php echo renderActionForConnection($connection3); ?>" target="_blank">
                                <img src="<?php echo displayIconForConnection($connection3); ?>" class="center" />
                            </a>
                        </div>
                        <div class="col4">
                            <a href="<?php echo renderActionForConnection($connection4); ?>" target="_blank">
                                <img src="<?php echo displayIconForConnection($connection4); ?>" class="center" />
                            </a>
                        </div>
                        <div class="col4">
                            <a href="mailto:?cc=<?php echo $currRecord->email; ?>&subject=I have a referral for you!&body=I would like to introduce you to <?php echo htmlentities($currRecord->first_name); ?> <?php echo htmlentities($currRecord->last_name); ?><?php echo (!empty($orgName) ? " from " . htmlentities($orgName) : ""); ?>.<?php echo (!empty($directoryName) ? " I met " . htmlentities($currRecord->first_name) . " through " . htmlentities($directoryName) . " (". $customPlatformUrl ."/" . $mainEzcardId . ")." : ""); ?><?php echo (!empty($ezcardId) ? " Check out ".htmlentities($currRecord->first_name)."'s digital card: ". $customPlatformUrl ."/" . $ezcardId : ""); ?>" target="_blank">
                                <img src="https://drive.google.com/thumbnail?id=11yJl07-zDjItJb7Dpfi8Cg8daRv7gxhT" class="center" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageIndex++;
        } ?>
    </div>
    <?php if (!empty($footerImage)) { ?>
        <div class="cards-footer">
            <img src="<?php echo $footerImage; ?>" style="width:100%;"/>
        </div>
        <div style="clear:both;"></div>
    <?php } ?>
</div>
<script type="text/javascript">

    if (typeof ezCardMember<?php echo buildControllerClassFromUri($directoryId); ?> !== "function")
    {
        ezCardMember<?php echo buildControllerClassFromUri($directoryId); ?> = function()
        {
            let self = this;
            let directoryNum = <?php echo $objDirectory->member_directory_id; ?>;
            let el;
            let errors;
            let pageMax = <?php echo $pageMax; ?>;
            let pageDisplayMax = <?php echo $pageMax; ?>;
            let cardRecordTotal = <?php echo $objDirectoryRecordResult->Data->Count(); ?>;
            let cardDisplayTotal = <?php echo $objDirectoryRecordResult->Data->Count(); ?>;
            let currPage = 1;
            let pageStart = 0;
            let pageEnd = 0;
            let lastPage = 0;

            const toggle = function(el)
            {
                if (typeof el.target === "undefined")
                {
                    return;
                }

                let targetEl = el.target;

                if (!el.target.classList.contains("cardRecord"))
                {
                    targetEl = getMainElement(el.target.parentElement, 0);
                }

                let cardDetails = targetEl.getElementsByClassName("card-details")[0];

                if (cardDetails.classList.contains("openRecord"))
                {
                    cardDetails.classList.remove("openRecord");
                    closeAll(cardDetails);
                    slideUp(cardDetails);
                    return false;
                }

                closeAll();
                cardDetails.classList.add("openRecord");
                slideDown(cardDetails);

                return true
            }

            const getMainElement = function(el)
            {
                if (!el.classList.contains("cardRecord"))
                {
                    return getMainElement(el.parentElement)
                }

                return el;
            }

            const closeAll = function(except)
            {
                let cardList = getAllRecrds();

                for(let currCardList of Array.from(cardList))
                {
                    let cardDetails = currCardList.getElementsByClassName("card-details")[0];

                    if (except && cardDetails.isEqualNode(except))
                    {
                        continue;
                    }

                    cardDetails.style.display = "none";
                }
            }

            const getAllRecrds = function()
            {
                if (el === null)
                {
                    return [];
                }

                let CardsContainer = el.getElementsByClassName("cards-container")[0];

                if (CardsContainer === null)
                {
                    return [];
                }

                let allCards = el.getElementsByClassName("cards-container")[0].getElementsByClassName("cardRecord");
                return allCards;
            }

            const paginate = function(cardList)
            {
                if (!cardList)
                {
                    cardList = getAllRecrds();
                }

                if (cardList.length === 0) { return; }

                let intCardIndex = 1;
                let intTotalIndex = 0;
                let intPaginationIndex = 1;

                for (let currCardList of Array.from(cardList))
                {
                    let currCardAllow = currCardList.getAttribute("data-filter-allow");

                    if (currCardAllow !== "true")
                    {
                        continue;
                    }

                    intTotalIndex++;

                    currCardList.setAttribute("data-pagination", intPaginationIndex);

                    if (intCardIndex >= pageMax)
                    {
                        intCardIndex = 0;
                        intPaginationIndex++;
                    }

                    intCardIndex++;
                }

                elm("totalCardRecords-<?php echo $directoryId; ?>").innerText = intTotalIndex.toString();
                cardDisplayTotal = intTotalIndex;

                console.log(cardDisplayTotal);
                console.log(cardList);
                console.log(cardList.length);

                renderPagination(cardList);
            }

            const renderPagination = function(cardList)
            {
                if (!cardList) {
                    cardList = getAllRecrds();
                }

                cardRecordTotal = cardList.length;

                for (let currCardList of Array.from(cardList))
                {
                    let currCardPagination = parseFloat(currCardList.getAttribute("data-pagination"));
                    let currCardAllow = currCardList.getAttribute("data-filter-allow");

                    if (currCardPagination === currPage && currCardAllow === "true")
                    {
                        currCardList.style.display = "block";
                    }
                    else
                    {
                        currCardList.style.display = "none";
                    }
                }

                updatePaginationPageStartEnd();
                updatePaginationDisplay();
            }

            const prevPage = function()
            {
                if (currPage === 1) { return; }
                currPage--;
                renderPagination();
            }

            const nextPage = function()
            {
                let currPageDisplayMax = pageDisplayMax;
                if (currPageDisplayMax > cardDisplayTotal)
                {
                    currPageDisplayMax = cardDisplayTotal;
                }

                let lastPage = Math.ceil(cardDisplayTotal/currPageDisplayMax);
                if (currPage === lastPage) { return; }

                currPage++;
                renderPagination();
            }

            const updatePaginationPageStartEnd = function()
            {
                let currPageDisplayMax = pageDisplayMax;

                if (currPageDisplayMax > cardDisplayTotal)
                {
                    currPageDisplayMax = cardDisplayTotal;
                }

                pageStart = ((currPageDisplayMax * (currPage - 1)) + 1);
                if (pageStart < 1 ) { pageStart = 1; }
                document.getElementById("currentPageStart-<?php echo $directoryId; ?>").innerHTML = pageStart.toString();

                pageEnd = (pageDisplayMax * (currPage));
                if (pageEnd > cardDisplayTotal)
                {
                    pageEnd = cardDisplayTotal;
                }

                if (pageEnd > cardRecordTotal) { pageEnd = cardRecordTotal; }

                document.getElementById("currentPageEnd-<?php echo $directoryId; ?>").innerHTML = pageEnd.toString();

                lastPage = Math.ceil(cardDisplayTotal/currPageDisplayMax);
            }

            const updatePaginationDisplay = function()
            {
                let prevPageButton = document.getElementById("prev-card-page-<?php echo $directoryId; ?>").classList;
                let nextPageButton = document.getElementById("next-card-page-<?php echo $directoryId; ?>").classList;
                if (currPage === 1) { prevPageButton.add("cards-page-button-disabled"); } else { prevPageButton.remove("cards-page-button-disabled"); }
                if (currPage === lastPage) { nextPageButton.add("cards-page-button-disabled"); } else { nextPageButton.remove("cards-page-button-disabled"); }
            }

            const toggleAddRecordRequest = function(event)
            {
                clearErrors();
                el.getElementsByClassName("cards-options-add-via-signin")[0].style.display = "none";
                el.getElementsByClassName("cards-options-filter")[0].style.display = "none";
                slideToggle(el.getElementsByClassName("cards-options-add-record")[0]);
            }

            const toggleCardFilter = function(event)
            {
                clearErrors();
                el.getElementsByClassName("cards-options-add-via-signin")[0].style.display = "none";
                el.getElementsByClassName("cards-options-add-record")[0].style.display = "none";
                slideToggle(el.getElementsByClassName("cards-options-filter")[0]);
            }

            const changePagination = function(event)
            {
                let value = el.getElementsByClassName("pagination-max-count")[0].value;
                pageDisplayMax = value;
                pageMax = value;
                paginate();
            }

            const toggleSignInToEzDigital = function()
            {
                clearErrors();
                el.getElementsByClassName("cards-options-add-record")[0].style.display = "none";
                el.getElementsByClassName("cards-options-filter")[0].style.display = "none";
                slideToggle(el.getElementsByClassName("cards-options-add-via-signin")[0]);
            }

            const createEzDigitalAccount = function()
            {
                clearErrors();
                slideUp(el.getElementsByClassName("cards-options-add-via-signin")[0], 250, function() {
                    slideDown(el.getElementsByClassName("cards-options-add-record")[0]);
                });
            }

            const closeParentDialog = function(event)
            {
                clearErrors();
                let parentClass = event.target.getAttribute("data-parent");
                slideUp(el.getElementsByClassName(parentClass)[0]);
            }

            const filterMemberList = function(event)
            {
                // data-filter-first-name

                let filterFirstName = elm("filter_first_name-<?php echo $directoryId; ?>").value.toLowerCase();
                let filterLastName = elm("filter_last_name-<?php echo $directoryId; ?>").value.toLowerCase();
                let filterOrgName = elm("filter_origanization-<?php echo $directoryId; ?>").value.toLowerCase();
                let cardList = getAllRecrds();

                cardRecordTotal = cardList.length;

                for (let currCardList of Array.from(cardList))
                {
                    let blnAllowRecord = true;

                    if (filterFirstName !== "" && !currCardList.getAttribute("data-filter-first-name").toLowerCase().includes(filterFirstName)) {
                        blnAllowRecord = false;
                    }
                    if (filterLastName !== "" && !currCardList.getAttribute("data-filter-last-name").toLowerCase().includes(filterLastName)) {
                        blnAllowRecord = false;
                    }
                    if (filterOrgName !== "" && !currCardList.getAttribute("data-filter-organization").toLowerCase().includes(filterOrgName)) {
                        blnAllowRecord = false;
                    }

                    currCardList.setAttribute("data-filter-allow", blnAllowRecord);
                }

                paginate(cardList);
            }

            const submitEzDigitalAccount = function(event)
            {
                let entity = {};
                entity.username = classFirst("username-<?php echo $directoryId; ?>");
                entity.password = classFirst("password-<?php echo $directoryId; ?>");

                if (!formValidation(entity, {
                    username: "required",
                    password: "required",
                }))
                {
                    return;
                }

                let entityData = getValuesFromEntity(entity);
                entityData.directory_id = directoryNum;

                engageFloatShield();

                sendAjax("/api/v1/directories/public-full-page/sign-in-user-account", entityData, function (result)
                {
                    if (result.success == false)
                    {
                        closeFloatShield();

                        slideUp(el.getElementsByClassName("cards-options-add-via-signin")[0], 250, function() {
                            classFirst("cards-options-add-via-signing-error-text").innerHTML =  '<i class="fa fa-warning"></i> ' + result.message;
                            slideDown(el.getElementsByClassName("cards-options-add-via-signin-error")[0], 250);
                        });

                        return;
                    }

                    closeFloatShield();

                    slideUp(el.getElementsByClassName("cards-options-add-via-signin")[0], 250, function() {
                        slideDown(el.getElementsByClassName("cards-options-add-via-signin-success")[0]);
                    });
                });
            }

            const submitNewEzDigitalAccount = function(event)
            {
                let entity = {};
                entity.first_name = classFirst("first-name-<?php echo $directoryId; ?>");
                entity.last_name = classFirst("last-name-<?php echo $directoryId; ?>");
                entity.email = classFirst("email-<?php echo $directoryId; ?>");
                entity.phone = classFirst("phone-<?php echo $directoryId; ?>");
                entity.title = classFirst("title-<?php echo $directoryId; ?>");
                entity.company = classFirst("company-<?php echo $directoryId; ?>");
                entity.industry = classFirst("industry-<?php echo $directoryId; ?>");
                entity.username = classFirst("username-new-<?php echo $directoryId; ?>");
                entity.password = classFirst("password-new-<?php echo $directoryId; ?>");
                entity.password2 = classFirst("password-new-<?php echo $directoryId; ?>-2");

                if (!formValidation(entity, {
                    first_name: "required",
                    last_name: "required",
                    email: "required|email",
                    phone: "required|phone",
                    title: "required",
                    company: "required",
                    industry: "required",
                    username: "required",
                    password: "required|passwordComplex",
                    password2: "required|sameAs:password|password:complex",
                }))
                {
                    return;
                }

                let entityData = getValuesFromEntity(entity);
                entityData.directory_id = directoryNum;

                engageFloatShield();

                sendAjax("/api/v1/directories/public-full-page/create-user-account-dr-lydie", entityData, function (result)
                {
                    if (result.success == false)
                    {
                        closeFloatShield();

                        slideUp(el.getElementsByClassName("cards-options-add-record")[0], 250, function() {
                            classFirst("cards-options-add-record-error-text").innerHTML =  '<i class="fa fa-warning"></i> ' + result.message;
                            slideDown(el.getElementsByClassName("cards-options-add-record-error")[0], 250);
                        });

                        return;
                    }

                    closeFloatShield();

                    slideUp(el.getElementsByClassName("cards-options-add-record")[0], 250, function() {
                        slideDown(el.getElementsByClassName("cards-options-add-record-success")[0]);
                    });
                });
            }

            const sendAjax = function(url, data, callback)
            {
                let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function()
                {
                    if (this.readyState == 4 && this.status == 200)
                    {
                        if (typeof callback === "function")
                        {
                            callback(JSON.parse(this.responseText));
                        }
                    }
                };

                xhttp.open("POST", url, true);
                xhttp.setRequestHeader("Content-type", "application/json;charset=UTF-8");
                xhttp.send(JSON.stringify(data));
            }

            const getValuesFromEntity = function(entity)
            {
                let newEntity = {};

                for( let currEntityFieldName in entity)
                {
                    newEntity[currEntityFieldName] = entity[currEntityFieldName].value;
                }

                return newEntity;
            }

            const formValidation = function(entity, validation)
            {
                let validForm = true;
                const keys = Object.keys(validation);

                keys.forEach((key, index) =>
                {
                    let currValidationField = key

                    if(entity[key])
                    {
                        let currRules = validation[key];
                        let currEntityField = entity[key];
                        let currValidationResult = validateRules(currEntityField, currValidationField, currRules, entity);

                        if (currValidationResult.success == false)
                        {
                            validForm = false;
                            displayValidationError(currValidationResult);
                        }
                    }
                });

                return validForm;
            }

            const validateRules = function(entityField, validationField, rules, entity)
            {
                let colRules = rules.split("|");

                for (let currRule of  Array.from(colRules))
                {
                    if (currRule.includes(":"))
                    {
                        let conditionalRule = currRule.split(":");

                        switch (conditionalRule[0])
                        {
                            case "sameAs":
                                if (entity[conditionalRule[1]] && entity[conditionalRule[1]].value !== entityField.value)
                                {
                                    return validationObject(false, validationField, entityField, validationField + " needs to match " + ucWords(conditionalRule[1]) + ".");
                                }
                                break;
                        }
                    }
                    else
                    {
                        switch(currRule)
                        {
                            case "required":
                                if (!entityField.value || typeof entityField.value === "undefined" || entityField.value === "")
                                {
                                    return validationObject(false, validationField, entityField, validationField + " cannot be blank.");
                                }
                                break;
                            case "passwordComplex":
                                if (!isComplexPassword(entityField.value))
                                {
                                    return validationObject(false, validationField, entityField, validationField + " isn't complex enough.");
                                }
                                break;
                            case "email":
                                if (!isEmail(entityField.value))
                                {
                                    return validationObject(false, validationField, entityField, validationField + " isn't an email address.");
                                }
                                break;
                        }
                    }
                }

                return validationObject(true, validationField, entityField, "Passes validation.");
            }

            const ucWords = function(text)
            {
                return text.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            }

            const isEmail = function(email)
            {
                return /^([a-zA-Z0-9\.\+])*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
            }

            const isComplexPassword = function(str)
            {
                let strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
                return strongRegex.test(str);
            }

            const validationObject = function(result, name, entityField, message)
            {
                return {success: result, name: name, entity: entityField, error: message};
            }

            const displayValidationError = function(validationResult)
            {
                let errorMessageClass = validationResult.name + "-error-text";
                validationResult.entity.classList.add("error-validation");
                validationResult.entity.setAttribute("data-error-text", errorMessageClass);
                onBlur(validationResult.entity, removeValidationDisplay);
                if (classFirst(errorMessageClass)) {
                    return; }
                let errorNode  = document.createElement("div");
                errorNode.classList.add(errorMessageClass);
                errorNode.classList.add("error-message-text");
                errorNode.innerHTML = validationResult.error;
                afterNode(validationResult.entity, errorNode);
            }

            const clearErrors = function()
            {
                let errorNodes = classList("error-validation");

                for (let currNode of  Array.from(errorNodes))
                {
                    let nodeClass = currNode.getAttribute("data-error-text");
                    let errorTextNode = classFirst(nodeClass);
                    errorTextNode.remove();
                    currNode.removeEventListener("click", removeValidationDisplay);
                    currNode.classList.add("error-validation-complete");
                }

                let errorNodesFix = classList("error-validation-complete");

                for (let currNode of  Array.from(errorNodesFix))
                {
                    currNode.classList.remove("error-validation");
                }
            }

            const removeValidationDisplay = function (el)
            {
                el.currentTarget.removeEventListener("click", removeValidationDisplay);
                el.currentTarget.classList.remove("error-validation");
                const errorClass = el.currentTarget.getAttribute("data-error-text");
                classFirst(errorClass).remove();
            }

            const slideUp = function(target, duration = 250, callback)
            {
                target.style.transitionProperty = 'height, margin, padding';
                target.style.transitionDuration = duration + 'ms';
                target.style.boxSizing = 'border-box';
                target.style.height = target.offsetHeight + 'px';
                target.offsetHeight;
                target.style.overflow = 'hidden';
                target.style.height = 0;
                target.style.paddingTop = 0;
                target.style.paddingBottom = 0;
                target.style.marginTop = 0;
                target.style.marginBottom = 0;
                window.setTimeout( () => {
                    target.style.display = 'none';
                    target.style.removeProperty('height');
                    target.style.removeProperty('padding-top');
                    target.style.removeProperty('padding-bottom');
                    target.style.removeProperty('margin-top');
                    target.style.removeProperty('margin-bottom');
                    target.style.removeProperty('overflow');
                    target.style.removeProperty('transition-duration');
                    target.style.removeProperty('transition-property');
                    if (typeof callback === "function") {
                        callback();
                    }
                }, duration);
            }

            const slideDown = function(target, duration = 250, callback)
            {
                target.style.removeProperty('display');
                let display = window.getComputedStyle(target).display;

                if (display === 'none')
                    display = 'block';

                target.style.display = display;
                let height = target.offsetHeight;
                target.style.overflow = 'hidden';
                target.style.height = 0;
                target.style.paddingTop = 0;
                target.style.paddingBottom = 0;
                target.style.marginTop = 0;
                target.style.marginBottom = 0;
                target.offsetHeight;
                target.style.boxSizing = 'border-box';
                target.style.transitionProperty = "height, margin, padding";
                target.style.transitionDuration = duration + 'ms';
                target.style.height = height + 'px';
                target.style.removeProperty('padding-top');
                target.style.removeProperty('padding-bottom');
                target.style.removeProperty('margin-top');
                target.style.removeProperty('margin-bottom');
                window.setTimeout( () => {
                    target.style.removeProperty('height');
                    target.style.removeProperty('overflow');
                    target.style.removeProperty('transition-duration');
                    target.style.removeProperty('transition-property');
                    if (typeof callback === "function") {
                        callback();
                    }
                }, duration);
            }

            const slideToggle = (target, duration = 250) => {
                if (window.getComputedStyle(target).display === 'none') {
                    return slideDown(target, duration);
                } else {
                    return slideUp(target, duration);
                }
            }

            const elm = function(element)
            {
                return document.getElementById(element);
            }

            const classList = function(element, callback)
            {
                if (typeof callback === "function")
                {
                    let elm = el.getElementsByClassName(element);

                    for (let currElm of Array.from(elm))
                    {
                        callback(currElm);
                    }

                    return elm;
                }

                return el.getElementsByClassName(element);
            }

            const globalClassList = function(element)
            {
                return document.getElementsByClassName(element);
            }

            const globalTagList = function(tag)
            {
                return document.getElementsByTagName(tag);
            }

            const classFirst = function(element)
            {
                return el.getElementsByClassName(element)[0];
            }

            const globalClassLast = function(element)
            {
                let classList = globalClassList(element);
                return classList[classList.length - 1];
            }

            const onBlur = function(element, callback)
            {
                return element.addEventListener("click", removeValidationDisplay);
            }

            const afterNode = function (target, node)
            {
                target.parentNode.insertBefore(node, target.nextSibling)
            }

            const appendNode = function (target, node)
            {
                target.insertBefore(node, target.children[target.children.length - 1].nextSibling)
            }

            const engageFloatShield = function (callback)
            {
                let shield  = document.createElement("div");
                shield.classList.add("universal-float-shield");
                appendNode(globalTagList("body")[0], shield);

                if (typeof callback === 'function') {
                    callback();
                }
            };

            const closeFloatShield = function (callback, intTimeout)
            {
                let intTimeOutLength = 0;

                if ( intTimeout )
                {
                    intTimeOutLength = intTimeout;
                }

                window.setTimeout(function ()
                {
                    let shieldNew = globalClassLast("universal-float-shield");

                    if (shieldNew)
                    {
                        shieldNew.remove();
                    }

                    if (typeof callback === 'function') {
                        callback();
                    }

                }, intTimeOutLength);
            };

            this.load = function(id)
            {
                let self = this;
                if (elm("prev-card-page-<?php echo $directoryId; ?>") === null) {
                    setTimeout(function() {
                        self.load(id);
                    },500)
                    return;
                }

                el = document.getElementById(id);
                let cardList = getAllRecrds();

                let touchEvent = 'ontouchstart' in window ? 'touchend' : 'click';

                for (let currCardList of Array.from(cardList))
                {
                    currCardList.getElementsByClassName("card-label")[0].addEventListener(touchEvent, function(event){
                        event.preventDefault();
                        try {
                            toggle(event);
                        }
                        catch(ex)
                        {
                            alert(ex);
                        }
                    });
                }

                paginate(cardList);

                elm("prev-card-page-<?php echo $directoryId; ?>").addEventListener(touchEvent, function(event){ prevPage(event); });
                elm("next-card-page-<?php echo $directoryId; ?>").addEventListener(touchEvent, function(event){ nextPage(event); });
                elm("submitSendEmail1-<?php echo $directoryId; ?>").addEventListener(touchEvent, function(event){ submitNewEzDigitalAccount(event); });
                elm("submitSendEmail2-<?php echo $directoryId; ?>").addEventListener(touchEvent, function(event){ submitEzDigitalAccount(event); });
                elm("filter_first_name-<?php echo $directoryId; ?>").addEventListener("keyup", function(event){ filterMemberList(event); });
                elm("filter_last_name-<?php echo $directoryId; ?>").addEventListener("keyup", function(event){ filterMemberList(event); });
                elm("filter_origanization-<?php echo $directoryId; ?>").addEventListener("keyup", function(event){ filterMemberList(event); });

                classList("sign-in-to-ez-digital-<?php echo $directoryId; ?>", function(elm) {
                    elm.addEventListener(touchEvent, function(event){ classFirst("cards-options-add-record-error").style.display = "none"; createEzDigitalAccount(event); });
                });
                classList("card-filter-<?php echo $directoryId; ?>")[0].addEventListener(touchEvent, function(event){ toggleCardFilter(event); });
                classList("pagination-max-count")[0].addEventListener("change", function(event){ changePagination(event); });
                classList("add-record-request-<?php echo $directoryId; ?>", function (elm) {
                    elm.addEventListener(touchEvent, function(event){ toggleAddRecordRequest();  });
                });
                classList("create-ez-digital-account", function (elm) {
                    elm.addEventListener(touchEvent, function(event){ createEzDigitalAccount(); });
                });
                classList("close-parent-dialog", function (elm) {
                    elm.addEventListener(touchEvent, function(event){ closeParentDialog(event); });
                });
            }
        };
    }

    let myEzcardMember<?php echo buildControllerClassFromUri($directoryId); ?> = new ezCardMember<?php echo buildControllerClassFromUri($directoryId); ?>();
    myEzcardMember<?php echo buildControllerClassFromUri($directoryId); ?>.load('<?php echo $directoryId; ?>');

</script>
