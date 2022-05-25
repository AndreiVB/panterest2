/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./scss/app.scss";

import $ from "jquery";

import "bootstrap";

$(".custom-file-input").on("change", function () {
	let inputFile = e.currentTarget;
	$(inputFile)
		.parent()
		.find(".custom-file-label")
		.html(inputFile.files[0].name);
});

$(".alert").delay(7000).fadeOut("slow");

// $("#btn-logout").onclick(function () {
// 	$("#btn-logout").click(function () {
// 		console.log("Click called.");
// 	});
// 	$(".js-logout-form").submit();
// });
