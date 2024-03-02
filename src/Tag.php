<?php

namespace Northrook\Types;

enum Tag
{
	case body;
	case html;
	case li;
	case dropdown;
	case menu;
	case modal;
	case field;
	case fieldset;
	case legend;
	case label;
	case option;
	case select;
	case input;
	case textarea;
	case form;
	case tooltip;
	case section;
	case main;
	case header;
	case footer;
	case div;
	case span;
	case p;
	case ul;
	case a;
	case img;
	case button;
	case i;
	case strong;
	case em;
	case sup;
	case sub;
	case br;
	case hr;
	case h;
	case h1;
	case h2;
	case h3;
	case h4;

//	final public function __toString(): string {
//		return $this->name;
//	}
}