<?php

$marketplaceId = getGuid();

?>
<style data-name="ezcard-visualization-cards-css">
    @import url('https://fonts.googleapis.com/icon?family=Material+Icons');

    .ezcard-visualization-cards {
        font-size: 10pt !important;
        font-family: roboto,sans-serif !important;
        line-height: 15px;
        padding: 0 20px 0 25px;
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
        margin-bottom: 65px;
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
        position: relative;
    }
    .ezcard-visualization-cards .card-label .card-box {
        width: 100%;
        padding: 17px 17px 30px;
        display: flex;
        background: #3cc;
        color: #fff !important;
        z-index: 5;
        position: relative;
    }
    .ezcard-visualization-cards .card-label .card-box .card-title {
        margin-bottom:35px;
        font-family:Saira;
        width:80%;
    }
    .ezcard-visualization-cards .card-label .card-box .card-title-text {
        font-size:30px;
        padding-top:15px;
        position:relative;
        color: #000;
        line-height:35px;
    }
    .ezcard-visualization-cards .card-label .card-box .card-description {
        font-size:19px;
        line-height:23px;
        color: #000;
    }
    .ezcard-visualization-cards .card-label .card-box .card-title-text:after {
        content:" ";
        background: #ffffcc;
        position: absolute;
        height:5px;
        bottom:-8px;
        left:3px;
        width: calc(100% - 10px);
        z-index: 4;
    }
    .ezcard-visualization-cards .card-label:before {
        content:" ";
        background: #ffffcc;
        position: absolute;
        top:12px;
        bottom:-12px;
        left:-8px;
        right:8px;
        z-index: 4;
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
        overflow-wrap: break-word;
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
    .cards-header {
        position: relative;
    }
    .cards-header .today-only {
        width: 170px;
        height: 65px;
        position: absolute;
        left: -15px;
        bottom: 15px;
        border-radius: 75px / 31px;
        color: white;
        background-color: red;
        text-align: center;
        display: flex;
        align-content: center;
        justify-content: center;
        line-height: 61px;
        font-size: 22px;
        text-shadow: 2px 2px 2px black;
        transform: rotate(-7deg);
        z-index: 10;
    }

    .error-text .fa {
        position: relative;
        top: 2px;
    }
    .package-counter {
        color: #000;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 15px;
    }
    .pricing-shell {
        display:flex;
        flex-direction: row;
        margin-top: 22px;
    }
    .pricing-shell > div {
        display: flex;
        flex: 1 1 100%;
        justify-content: center;
    }
    .pricing-shell .only-today > span {
        width: 71px;
        height: 68px;
        position: absolute;
        left: 15px;
        bottom: -34px;
        border-radius: 130px;
        color: white;
        background-color: red;
        text-align: center;
        display: flex;
        align-content: center;
        justify-content: center;
        line-height: 19px;
        font-size: 15px;
        text-shadow: 2px 2px 2px black;
        z-index: 10;
        padding: 10px;
        border: 5px solid #ffffcc;
    }
    .pricing-shell .card-price {
        display: flex;
        flex-direction:column;
        position: relative;
    }
    .pricing-shell .card-price-inner {
        position: absolute;
        top: -18px;
        line-height: 30px;
        background: #000;
        padding: 11px;
        left: -75px;
        right: -75px;
        transform: rotate(-4deg);
        border-radius: 0 15px 0 14px;
    }
    .pricing-shell .card-price .card-price-inner {
        position: absolute;
        top:-14px;
    }
    .pricing-shell .card-price .card-price-inner > div {
        display: block;
        text-align: center;
        font-size:23px;
    }
    .pricing-shell .card-original-price {
        display: flex;
    }
    .pricing-shell .card-original-price-inner {
        position: absolute;
        right: 8px;
        bottom: -40px;
        background: #ffffcc;
        color: black;
        padding: 8px 12px;
        border-radius: 10px 0 0 0;
    }
    .card-price-inner .discount-price {
        font-weight: bold !important;
        font-size: 33px !important;
    }

    @media (max-width: 550px) {
        .ezcard-visualization-cards .mobile {
            margin: 10px auto 10px auto!important;
            float:none;
        }
        .ezcard-visualization-cards .mobile-bis {
            margin : 10px!important;
        }
        .pricing-shell .card-original-price-inner {
            bottom: -53px;
            width: 90px;
        }
        .pricing-shell .only-today > span {
            left: -5px;
            bottom: -44px;
        }
        .pricing-shell .card-price .card-price-inner > div {
            font-size: 19px;
        }
        .cards-header .today-only {
            left: -45px;
            bottom: -5px;
        }
        .pricing-shell .card-price .card-price-inner {
            position: absolute;
            top:-12px;
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
$sortField = $objDirectory->defaults->FindEntityByValue("label", "sort_by")->value ?? "order";
$sortOrder = $objDirectory->defaults->FindEntityByValue("label", "sort_order")->value ?? "asc";
$colDirectoryRecord->SortBy($sortField, $sortOrder);

?>
<div id="<?php echo $marketplaceId; ?>" class="ezcard-visualization-cards">
    <div class="cards-header">
        <img src="https://app.ezcardmedia.com/images/users/5865fe8b-111f-4c6f-a098-8118e5c83af2/07bcaa37bbdb7272cf672ab51df02e76884c2cee.jpg" style="width:100%;margin-bottom:25px;"/>
        <div class="today-only">Today Only</div>
    </div>
    <div class="cards-header-html">
        <?php echo $headerHtml; ?>
    </div>
    <div class="cards-container">
        <?php
        $intCount = 0;
        foreach($colDirectoryRecord as $currRecord)
        {
            ?>
            <?php

            $orgLogo = "/_ez/images/users/defaultLogo.png";

            if (!empty($currRecord->custom))
            {
                $originalRegularPrice = $currRecord->custom->FindEntityByValue("label", "original_regular_price")->value ?? "";
                $packageId = $currRecord->custom->FindEntityByValue("label", "package_id")->value ?? "";
            }

            $intCount++;

            $hexColor = (!empty($currRecord->hex_color) ? $currRecord->hex_color : $mainColor);
            ?>
            <div onclick="addMarketplacePackageToCart<?php echo buildControllerClassFromUri($marketplaceId); ?>(<?php echo $packageId; ?>)" class="cardRecord" data-card-index="<?php echo $pageIndex; ?>" data-card-total="<?php echo $pageMax; ?>" data-filter-allow="true" data-filter-first-name="<?php echo $currRecord->first_name; ?>" data-filter-last-name="<?php echo $currRecord->last_name; ?>" data-filter-organization="<?php echo $orgName; ?>">
                <div class="card-label" data-toggle-visibility="controller">
                    <div class="card-box">
                        <div class="col1" style="display: none;">
                            <img src="<?php echo (!empty($currRecord->profile_image_url) ? $currRecord->profile_image_url : "/_ez/images/users/defaultAvatar.jpg"); ?>" width="82px" height="82px" style="border-radius:10%"class="centered-and-cropped" onerror="imgError(this);" />
                        </div>
                        <div class="col2" >
                                <div class="package-counter">Package <?php echo $intCount; ?></div>
                            <?php if (!empty($currRecord->name)) { ?>
                                <div class="card-title"><span class="card-title-text"><?php echo $currRecord->name ?? "Package Name #" . $intCount; ?></span></div>
                            <?php } ?>
                            <?php if (!empty($currRecord->description)) { ?>
                                <div class="card-description"><?php echo $currRecord->description ?? "A small description for this package."; ?></div>
                                <button class="btn btn-Primary" style="margin-top:15px;width:100%;margin-bottom:15px;background-color:#ffffcc;border:0 solid #ffffcc;color:black;">Add To Cart</button>
                            <?php } ?>
                                <div class="pricing-shell">
                                    <div class="only-today"><span>Today<br>Only</span></div>
                            <?php if (!empty($currRecord->regular_price)) { ?>
                                    <div class="card-price">
                                        <div class="card-price-inner">
                                            <div class="original-price-example">A <?php echo $originalRegularPrice ?? "$0.00"; ?> Value for</div><div class="discount-price">$<?php echo $currRecord->regular_price ?? "0.00"; ?></div>
                                        </div>
                                    </div>
                            <?php } ?>
                            <?php if (!empty($originalRegularPrice)) { ?>
                                    <div class="card-original-price">
                                        <div class="card-original-price-inner">
                                            A savings of: $<?php echo str_replace(["$",","],"", $originalRegularPrice) - ($currRecord->regular_price ?? 0.00); ?>
                                        </div>
                                    </div>
                            <?php } ?>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="card-details" style="display:none;">
                    <div class="containerc">
                        <?php echo $orgAbout; ?>
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

    if (typeof addMarketplacePackageToCart<?php echo buildControllerClassFromUri($marketplaceId); ?> !== "function")
    {
        addMarketplacePackageToCart<?php echo buildControllerClassFromUri($marketplaceId); ?> = function(id)
        {
            appCart.addPackageToCart(id)
                .registerEntityListAndManager()

            modal.EngageFloatShield();

            let alertData = {title: "Item Added To Cart", html: "Go to your shopping cart?.<hr/><i>Clicking cancel will return you to your browsing.</i>"};

            modal.EngagePopUpConfirmation(alertData, function() {
                modal.CloseFloatShield(function() {
                    appCart.openCart("custom")
                        .setCartPrivacy(true)
                        .setCustomerByUuid(Cookie.get("userNum"))
                        .registerEntityListAndManager()
                    modal.CloseFloatShield();
                });
            }, 500, 115, true);

        }

    }
    if (typeof ezCardMember<?php echo buildControllerClassFromUri($marketplaceId); ?> !== "function")
    {
        ezCardMember<?php echo buildControllerClassFromUri($marketplaceId); ?> = function()
        {
            let self = this;
            let directoryNum = <?php echo $objDirectory->marketplace_id; ?>;
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

            const getMainElement = function(el)
            {
                if (!el.classList.contains("cardRecord"))
                {
                    return getMainElement(el.parentElement)
                }

                return el;
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

            const ucWords = function(text)
            {
                return text.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
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
                if (elm("prev-card-page-<?php echo $marketplaceId; ?>") === null) {
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
            }
        };
    }

    let myEzcardMember<?php echo buildControllerClassFromUri($marketplaceId); ?> = new ezCardMember<?php echo buildControllerClassFromUri($marketplaceId); ?>();
    myEzcardMember<?php echo buildControllerClassFromUri($marketplaceId); ?>.load('<?php echo $marketplaceId; ?>');

</script>
