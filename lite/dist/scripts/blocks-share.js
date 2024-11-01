(function (blocks, element, blockEditor, components) {

    //setup constants
    const el = element.createElement;
    const {registerBlockType} = blocks;
    const {InspectorControls, useBlockProps, useInnerBlocksProps, __experimentalColorGradientControl} = blockEditor;
    const {Fragment, useEffect} = element;
    const {
        BaseControl,
        TextControl,
        ToggleControl,
        PanelBody,
        Icon,
        RangeControl,
        Button,
        ButtonGroup,
        Dropdown,
        SelectControl,
        ColorIndicator,
        FlexItem,
        __experimentalHStack
    } = components;
    const {__} = wp.i18n;

    //swap button colors on hover in editor
    const hoverSwap = (event) => {
        var container = event.target.closest('.social-linkz-buttons');
        if (container) {
            var inverseHover = container.classList.contains('social-linkz-inverse-hover');
            var buttonHoverColor = container.getAttribute('data-button-hover-color');
            var iconHoverColor = container.getAttribute('data-icon-hover-color');
            var block = event.target.closest('.social-linkz-button');
            var iconBlock = block.querySelector('.social-linkz-button-icon');
            var labelWrapper = block.querySelector('.social-linkz-button-label-wrapper');

            if (event.type == 'mouseover') {

                block.setAttribute('data-button-color', block.style.getPropertyValue('--social-linkz-button-color'));
                if (buttonHoverColor) {
                    block.style.setProperty('--social-linkz-button-color', buttonHoverColor);
                }

                if (inverseHover) {
                    inverseHoverSwap(block);
                    return;
                }

                iconBlock.setAttribute('data-icon-color', iconBlock.style.color);
                iconBlock.style.color = iconHoverColor || iconBlock.style.color;
                labelWrapper.style.color = iconHoverColor || iconBlock.style.color;
            } else if (event.type == 'mouseout') {

                block.style.setProperty('--social-linkz-button-color', block.getAttribute('data-button-color'));

                if (inverseHover) {
                    inverseHoverSwap(block);
                    return;
                }

                iconBlock.style.color = iconBlock.getAttribute('data-icon-color');
                labelWrapper.style.color = iconBlock.getAttribute('data-icon-color');
            }
        }

        function inverseHoverSwap(block) {
            block.classList.contains('social-linkz-hover-swap') ? block.classList.remove('social-linkz-hover-swap') : block.classList.add('social-linkz-hover-swap');
            var iconBorderBlocks = block.querySelectorAll('.social-linkz-button-icon.social-linkz-border');
            (iconBorderBlocks.length ? iconBorderBlocks : block.querySelectorAll('.social-linkz-button-block')).forEach(function (e) {
                e.classList.contains("social-linkz-inverse") ? e.classList.remove("social-linkz-inverse") : e.classList.add("social-linkz-inverse");
            });
        }
    }

    var variations = [];
    var networkIcons = [];
    const isProVersion = socialLinkz.isPRO;

    // Setup variations.
    Object.keys(socialLinkz.networks.share).forEach(function (key) {
        const network = socialLinkz.networks.share[key];
        var iconParts = network.icon.match(/viewBox="(.*?)".*?d="(.*?)"/);
        var networkIcon = el('svg', {
                fill: 'currentColor',
                viewBox: iconParts[1]
            },
            el('path', {
                    d: iconParts[2]
                }
            )
        );

        variations.push({
            isDefault: key === 'facebook' ? true : false,
            name: key,
            title: network.name,
            icon: networkIcon,
            attributes: {
                network: key,
                icon: networkIcon
            }
        });

        networkIcons[key] = networkIcon;
    });

    variations.forEach((variation) => {
        if (variation.isActive) {
            return;
        }
        variation.isActive = (blockAttributes, variationAttributes) => blockAttributes.network === variationAttributes.network;
    });

    // Register share block.
    registerBlockType('social-linkz/share', {
        apiVersion: 2,
        title: __("Social Linkz Share Buttons", 'social-linkz'),
        description: __("Add share buttons for your social network profiles.", 'social-linkz'),
        category: 'widgets',
        icon: 'share',
        keywords: ["share", "buttons", "social", "soc"],
        supports: {
            html: false
        },
        attributes: {
            id: {
                type: 'string'
            },
            buttonStyle: {
                type: 'string'
            },
            buttonLayout: {
                type: 'string'
            },
            alignment: {
                type: 'string',
                default: ''
            },
            buttonSize: {
                type: 'string',
                default: ''
            },
            buttonShape: {
                type: 'string',
                default: ''
            },
            buttonMargin: {
                type: 'number'
            },
            showLabels: {
                type: 'boolean'
            },
            buttonColor: {
                type: 'string'
            },
            iconColor: {
                type: 'string'
            },
            buttonHoverColor: {
                type: 'string'
            },
            iconHoverColor: {
                type: 'string'
            },
            inverseHover: {
                type: 'boolean'
            },
            mobileBreakpoint: {
                type: 'number'
            },
            hideAboveBreakpoint: {
                type: 'boolean'
            },
            hideBelowBreakpoint: {
                type: 'boolean'
            },
            totalShareCount: {
                type: 'boolean'
            },
            totalShareCountPosition: {
                type: 'string',
                default: ''
            },
            totalShareCountColor: {
                type: 'string'
            },
            networkShareCounts: {
                type: 'boolean'
            },
            ctaText: {
                type: 'string'
            },
            ctaSize: {
                type: 'number'
            },
            ctaColor: {
                type: 'string'
            },
            styleClasses: {
                type: 'object',
                default: {
                    containerClass: '',
                    buttonClass: '',
                    iconClass: '',
                    labelClass: ''
                }
            }
        },
        providesContext: {
            'buttonStyle': 'buttonStyle',
            'buttonLayout': 'buttonLayout',
            'buttonShape': 'buttonShape',
            'buttonMargin': 'buttonMargin',
            'buttonSize': 'buttonSize',
            'showLabels': 'showLabels',
            'buttonColor': 'buttonColor',
            'iconColor': 'iconColor',
            'buttonHoverColor': 'buttonHoverColor',
            'iconHoverColor': 'iconHoverColor',
            'inverseHover': 'inverseHover',
            'networkShareCounts': 'networkShareCounts',
            'styleClasses': 'styleClasses'
        },

        //add hover data attributes to wrapper
        getEditWrapperProps(attributes) {
            return {
                'data-button-hover-color': attributes.buttonHoverColor,
                'data-icon-hover-color': attributes.iconHoverColor
            };
        },

        edit: (function (props) {

            props.setAttributes({
                id: props.clientId,
            });

            useEffect(() => {

                const styleClasses = (() => {

                    var containerClass = '', shapeClass, buttonClass, iconClass = '', labelClass = '';

                    //build container class
                    if (props.attributes.buttonLayout) {
                        containerClass += " social-linkz-columns social-linkz-" + props.attributes.buttonLayout;
                    }
                    if (props.attributes.buttonSize) {
                        containerClass += " " + props.attributes.buttonSize;
                    }
                    if (props.attributes.totalShareCount) {
                        containerClass += " social-linkz-has-total-share-count-";
                        containerClass += props.attributes.totalShareCountPosition ? props.attributes.totalShareCountPosition : 'after';
                    }
                    if (props.attributes.inverseHover) {
                        containerClass += " social-linkz-inverse-hover";
                    }

                    shapeClass = props.attributes.buttonShape ? ' social-linkz-' + props.attributes.buttonShape : '';

                    if (props.attributes.buttonStyle) {
                        if (props.attributes.buttonStyle == 'inverse') {
                            buttonClass = " social-linkz-inverse";
                            iconClass += " social-linkz-inverse social-linkz-border" + shapeClass;
                            labelClass += " social-linkz-inverse";
                        } else if (props.attributes.buttonStyle == 'solid-inverse-border') {
                            buttonClass = " social-linkz-inverse" + shapeClass;
                            labelClass += " social-linkz-border social-linkz-inverse";
                        } else if (props.attributes.buttonStyle == 'full-inverse-border') {
                            buttonClass = " social-linkz-inverse social-linkz-border" + shapeClass;
                            iconClass += " social-linkz-inverse";
                            labelClass += " social-linkz-inverse";
                        } else if (props.attributes.buttonStyle == 'solid-inverse') {
                            buttonClass = " social-linkz-inverse";
                            iconClass += shapeClass;
                            labelClass += " social-linkz-inverse";
                        } else if (props.attributes.buttonStyle == 'full-inverse') {
                            buttonClass = " social-linkz-inverse" + shapeClass;
                            iconClass += " social-linkz-inverse";
                            labelClass += " social-linkz-inverse";
                        }
                    } else {
                        buttonClass = shapeClass;
                    }

                    if ( !props.attributes.showLabels) {
                        labelClass += ' social-linkz-hide';
                    }

                    return {
                        'containerClass': containerClass,
                        'buttonClass': buttonClass,
                        'iconClass': iconClass,
                        'labelClass': labelClass
                    };

                })();

                props.setAttributes({
                    styleClasses: styleClasses
                })

            }, [
                props.attributes.buttonStyle,
                props.attributes.buttonLayout,
                props.attributes.buttonSize,
                props.attributes.buttonShape,
                props.attributes.showLabels,
                props.attributes.inverseHover,
                props.attributes.totalShareCount,
                props.attributes.totalShareCountPosition
            ]);

            const blockProps = useBlockProps({
                className: 'social-linkz-buttons social-linkz-block-' + props.attributes.id + props.attributes.styleClasses.containerClass,
                style: {width: '100%'}
            });

            const innerBlocksProps = useInnerBlocksProps(blockProps, {
                allowedBlocks: ['social-linkz/share-network'],
                orientation: 'horizontal',
                placeholder: (() => {

                    if ( !props.isSelected) {
                        return el('div', {
                                style: {
                                    display: 'flex',
                                    alignItems: 'center'
                                }
                            },
                            el(Icon, {
                                icon: 'share',
                                style: {
                                    marginRight: '5px'
                                }
                            }),
                            __('Share Buttons', 'social-linkz')
                        );
                    } else {
                        return el("span", {}, __('Add Networks', 'social-linkz'));
                    }

                })()

            });

            const slColorPicker = (props, attribute, label) => {

                return el(Dropdown, {
                    className: 'social-linkz-color-picker',
                    style: {display: 'block'},
                    popoverProps: {
                        placement: 'top-left'
                    },
                    renderToggle: ({isOpen, onToggle}) => el(Button, {
                            style: {width: '100%'},
                            onClick: onToggle,
                            "aria-expanded": isOpen,
                        },
                        el(__experimentalHStack, {alignment: 'left'},

                            el(ColorIndicator, {
                                style: {background: props.attributes[attribute]}
                            }),

                            el(FlexItem, {}, label)
                        )
                    ),
                    renderContent: () => el('div', {
                            className: 'social-linkz-color-popover',
                            style: {padding: '8px', width: '244px'}
                        },
                        el(__experimentalColorGradientControl, {
                            colorValue: props.attributes[attribute],
                            onColorChange: newValue => props.setAttributes({
                                [attribute]: newValue
                            }),
                            disableCustomGradients: true
                        })
                    )
                });
            }

            const nsTotalShareCountOutput = (props) => {
                if (props.attributes.totalShareCount) {
                    return el('div', {
                            className: 'social-linkz-total-share-count',
                            style: {color: props.attributes.totalShareCountColor}
                        },
                        el('div', {className: 'social-linkz-total-share-count-wrapper'},
                            el('div', {className: 'social-linkz-total-share-count-details'},
                                el('div', {className: 'social-linkz-total-share-count-amount'}, '#'),
                                el('div', {className: 'social-linkz-total-share-count-text'}, __('SHARES', 'social-linkz'))
                            )
                        )
                    )
                }
            }

            return (
                el(Fragment, {},

                    //sidebar block controls
                    el(InspectorControls, {},
                        el(PanelBody, {
                                title: __('Settings', 'social-linkz'),
                                className: 'social-linkz-block-settings',
                                initialOpen: true
                            },

                            //button style
                            el(SelectControl, {
                                label: __('Button Style', 'social-linkz'),
                                value: props.attributes.buttonStyle,
                                onChange: (value) => {
                                    props.setAttributes({buttonStyle: value});
                                },
                                options: socialLinkz.block.design.buttonStyles,
                            }),

                            //button layout
                            el(SelectControl, {
                                label: __('Button Layout', 'social-linkz'),
                                value: props.attributes.buttonLayout,
                                onChange: (value) => {
                                    props.setAttributes({buttonLayout: value});
                                },
                                options: socialLinkz.block.design.buttonLayouts,
                            }),

                            //alignment
                            (() => {
                                if ( !props.attributes.buttonLayout) {
                                    return el(BaseControl, {label: __('Alignment', 'social-linkz')},
                                        el(ButtonGroup, {style: {display: 'flex'}},
                                            el(Button, {
                                                variant: 'secondary',
                                                isPressed: (() => {
                                                    return (props.attributes.alignment == '' ? true : false)
                                                })(),
                                                onClick: (event) => {
                                                    props.setAttributes({alignment: event.target.value})
                                                },
                                                value: '',
                                                icon: el(Icon, {icon: 'align-left', style: {pointerEvents: 'none'}}),
                                                style: {display: 'inline-flex', flexGrow: '1'}
                                            }),
                                            el(Button, {
                                                variant: 'secondary',
                                                isPressed: (() => {
                                                    return (props.attributes.alignment == 'center' ? true : false)
                                                })(),
                                                onClick: (event) => {
                                                    props.setAttributes({alignment: event.target.value})
                                                },
                                                value: 'center',
                                                icon: el(Icon, {icon: 'align-center', style: {pointerEvents: 'none'}}),
                                                style: {display: 'inline-flex', flexGrow: '1'}
                                            }),
                                            el(Button, {
                                                variant: 'secondary',
                                                isPressed: (() => {
                                                    return (props.attributes.alignment == 'right' ? true : false)
                                                })(),
                                                onClick: (event) => {
                                                    props.setAttributes({alignment: event.target.value})
                                                },
                                                value: 'right',
                                                icon: el(Icon, {icon: 'align-right', style: {pointerEvents: 'none'}}),
                                                style: {display: 'inline-flex', flexGrow: '1'}
                                            })
                                        )
                                    );
                                }
                            })(),

                            //button size
                            el(BaseControl, {label: __('Button Size', 'social-linkz')},
                                el(ButtonGroup, {style: {display: 'flex'}},
                                    el(Button, {
                                        variant: 'secondary',
                                        isPressed: (() => {
                                            return (props.attributes.buttonSize == 'small' ? true : false)
                                        })(),
                                        onClick: (event) => {
                                            props.setAttributes({buttonSize: event.target.value})
                                        },
                                        value: 'small',
                                        style: {display: 'inline-flex', flexGrow: '1', justifyContent: 'center'}
                                    }, __('Small', 'social-linkz')),
                                    el(Button, {
                                        variant: 'secondary',
                                        isPressed: (() => {
                                            return (props.attributes.buttonSize == '' ? true : false)
                                        })(),
                                        onClick: (event) => {
                                            props.setAttributes({buttonSize: event.target.value})
                                        },
                                        value: '',
                                        style: {display: 'inline-flex', flexGrow: '1', justifyContent: 'center'}
                                    }, __('Medium', 'social-linkz')),
                                    el(Button, {
                                        variant: 'secondary',
                                        isPressed: (() => {
                                            return (props.attributes.buttonSize == 'large' ? true : false)
                                        })(),
                                        onClick: (event) => {
                                            props.setAttributes({buttonSize: event.target.value})
                                        },
                                        value: 'large',
                                        style: {display: 'inline-flex', flexGrow: '1', justifyContent: 'center'}
                                    }, __('Large', 'social-linkz'))
                                )
                            ),

                            // Button shapes.
                            el(BaseControl, {label: __('Button Shape', 'social-linkz')},
                                el(ButtonGroup, {style: {display: 'flex'}},
                                    socialLinkz.block.design.buttonShapes.map(shape =>
                                        el(Button, {
                                            variant: 'secondary',
                                            isPressed: props.attributes.buttonShape === shape.value,
                                            onClick: (event) => {
                                                props.setAttributes({buttonShape: shape.value});
                                            },
                                            value: shape.value,
                                            style: {display: 'inline-flex', flexGrow: '1', justifyContent: 'center'}
                                        }, shape.label)
                                    )
                                )
                            ),

                            //button margin
                            el(RangeControl, {
                                label: __('Button Margin', 'social-linkz'),
                                onChange: (value) => {
                                    props.setAttributes({buttonMargin: value});
                                },
                                value: props.attributes.buttonMargin,
                                min: 0,
                                max: 20,
                                initialPosition: 10
                            }),

                            // Show labels.
                            el("div",
                                null,
                                isProVersion && el(ToggleControl, {
                                    label: __('Show Labels', 'social-linkz'),
                                    onChange: (value) => {
                                        props.setAttributes({showLabels: value});
                                    },
                                    checked: props.attributes.showLabels
                                })
                            ),

                            // Colors.
                            el(BaseControl, {
                                    label: __('Colors', 'social-linkz')
                                },
                                slColorPicker(props, 'buttonColor', __('Button Color', 'social-linkz')),
                                slColorPicker(props, 'buttonHoverColor', __('Button Hover Color', 'social-linkz')),
                                (() => {
                                    return !props.attributes.inverseHover ? slColorPicker(props, 'iconColor', __('Icon Color', 'social-linkz')) : ''
                                })(),
                                (() => {
                                    return !props.attributes.inverseHover ? slColorPicker(props, 'iconHoverColor', __('Icon Hover Color', 'social-linkz')) : ''
                                })()
                            ),

                            //inverse on hover
                            el(ToggleControl, {
                                label: __('Inverse on Hover', 'social-linkz'),
                                onChange: (value) => {
                                    props.setAttributes({inverseHover: value});
                                },
                                checked: props.attributes.inverseHover
                            }),
                        ),

                        //display settings
                        el("div",
                            null,
                            isProVersion && el(PanelBody, {
                                    title: __('Display', 'social-linkz'),
                                    className: '',
                                    initialOpen: false
                                },

                                //mobile breakpoint
                                el("div",
                                    null,
                                    isProVersion && el(RangeControl, {
                                        label: __('Mobile Breakpoint', 'social-linkz'),
                                        onChange: (value) => {
                                            props.setAttributes({mobileBreakpoint: value});
                                        },
                                        value: props.attributes.mobileBreakpoint,
                                        min: 400,
                                        max: 2000,
                                        initialPosition: 1200
                                    })
                                ),

                                // Hide above breakpoint
                                el("div",
                                    null,
                                    isProVersion && el(ToggleControl, {
                                        label: __('Hide Above Breakpoint', 'social-linkz'),
                                        onChange: (value) => {
                                            props.setAttributes({hideAboveBreakpoint: value});
                                        },
                                        checked: props.attributes.hideAboveBreakpoint
                                    })
                                ),

                                // Hide below breakpoint
                                el("div",
                                    null,
                                    isProVersion && el(ToggleControl, {
                                        label: __('Hide Below Breakpoint', 'social-linkz'),
                                        onChange: (value) => {
                                            props.setAttributes({hideBelowBreakpoint: value});
                                        },
                                        checked: props.attributes.hideBelowBreakpoint
                                    })
                                ),
                            )
                        ),

                        el("div",
                            null,
                            isProVersion && el(PanelBody, {
                                    title: __('Share Counts', 'social-linkz'),
                                    className: '',
                                    initialOpen: false
                                },

                                //total share count
                                el(ToggleControl, {
                                    label: __('Total Share Count', 'social-linkz'),
                                    onChange: (value) => {
                                        props.setAttributes({totalShareCount: value});
                                    },
                                    checked: props.attributes.totalShareCount
                                }),

                                //total share count options
                                (() => {
                                    if (props.attributes.totalShareCount) {
                                        return el(Fragment, {},
                                            el(BaseControl, {label: __('Total Share Count Position', 'social-linkz')},
                                                el(ButtonGroup, {style: {display: 'flex'}},
                                                    el(Button, {
                                                        variant: 'secondary',
                                                        isPressed: (() => {
                                                            return (props.attributes.totalShareCountPosition == 'before' ? true : false)
                                                        })(),
                                                        onClick: (event) => {
                                                            props.setAttributes({totalShareCountPosition: event.target.value})
                                                        },
                                                        value: 'before',
                                                        style: {display: 'inline-flex', flexGrow: '1'}
                                                    }, __('Before', 'social-linkz')),
                                                    el(Button, {
                                                        variant: 'secondary',
                                                        isPressed: (() => {
                                                            return (props.attributes.totalShareCountPosition == '' ? true : false)
                                                        })(),
                                                        onClick: (event) => {
                                                            props.setAttributes({totalShareCountPosition: event.target.value})
                                                        },
                                                        value: '',
                                                        style: {display: 'inline-flex', flexGrow: '1'}
                                                    }, __('After', 'social-linkz'))
                                                )
                                            ),
                                            el(BaseControl, {label: __('Color', 'social-linkz')},
                                                slColorPicker(props, 'totalShareCountColor', __('Total Share Count Color', 'social-linkz'))
                                            )
                                        );
                                    }
                                })(),

                                //network share counts
                                el(ToggleControl, {
                                    label: __('Network Share Counts', 'social-linkz'),
                                    onChange: (value) => {
                                        props.setAttributes({networkShareCounts: value});
                                    },
                                    checked: props.attributes.networkShareCounts
                                }),
                            )
                        ),

                        // Call To Action.
                        el("div",
                            null,
                            isProVersion && el(PanelBody, {
                                    title: __('Call to Action', 'social-linkz'),
                                    className: '',
                                    initialOpen: false
                                },

                                //text
                                el(TextControl, {
                                    label: __('Text', 'social-linkz'),
                                    value: props.attributes.ctaText,
                                    onChange: (value) => {
                                        props.setAttributes({ctaText: value});
                                    }
                                }),

                                //size
                                el(RangeControl, {
                                    label: __('Font Size', 'social-linkz'),
                                    onChange: (value) => {
                                        props.setAttributes({ctaSize: value});
                                    },
                                    value: props.attributes.ctaSize,
                                    min: 1,
                                    max: 100,
                                    initialPosition: 20
                                }),

                                //color
                                el(BaseControl, {
                                        label: __('Color', 'social-linkz')
                                    },
                                    slColorPicker(props, 'ctaColor', __('Font Color', 'social-linkz'))
                                )
                            )
                        ),
                    ),

                    el("div", innerBlocksProps,
                        el('div', {
                                className: 'social-linkz-inline-cta',
                                style: {
                                    fontSize: (props.attributes.ctaSize ?? 20) + 'px',
                                    color: props.attributes.ctaColor,
                                    textAlign: props.attributes.alignment
                                }
                            },
                            props.attributes.ctaText
                        ),

                        el("div", {
                                className: 'social-linkz-buttons-wrapper' + (props.attributes.alignment && !props.attributes.buttonLayout ? ' social-linkz-align-' + props.attributes.alignment : ''),
                                style: {
                                    gap: (props.attributes.buttonMargin ?? 10) + 'px'
                                }
                            },

                            (() => {
                                if (props.attributes.totalShareCountPosition) {
                                    return nsTotalShareCountOutput(props);
                                }
                            })(),

                            innerBlocksProps.children,

                            (() => {
                                if ( !props.attributes.totalShareCountPosition) {
                                    return nsTotalShareCountOutput(props);
                                }
                            })(),
                        )
                    )
                )
            );
        }),

        //save block
        save: function (props) {
            const block = useInnerBlocksProps.save(useBlockProps.save());
            return block.children;
        }
    });

    //register share network block
    registerBlockType('social-linkz/share-network', {
        apiVersion: 2,
        title: __('Follow Network', 'social-linkz'),
        description: __('Add an icon linking to a social network profile.', 'social-linkz'),
        parent: ['social-linkz/share'],
        supports: {reusable: false, html: false},
        attributes: {
            network: {type: "string"},
            styleClasses: {type: "object"}
        },
        usesContext: ['buttonStyle', 'buttonLayout', 'buttonShape', 'buttonMargin', 'buttonSize', 'showLabels', 'buttonColor', 'iconColor', 'buttonHoverColor', 'iconHoverColor', 'inverseHover', 'networkShareCounts', 'styleClasses'],

        edit: function (props) {

            useEffect(() => {
                props.setAttributes({
                    styleClasses: props.context.styleClasses
                })
            }, [props.context.styleClasses]);

            const networkShareCounts = props.context.networkShareCounts && ['twitter', 'facebook', 'pinterest', 'buffer', 'reddit', 'tumblr', 'vkontakte', 'yummly'].includes(props.attributes.network);

            return (

                el(Fragment, {},

                    //print block in editor
                    el('a', (useBlockProps)({
                            onMouseOver: hoverSwap,
                            onMouseOut: hoverSwap,
                            className: props.attributes.network + ' social-linkz-button' + (() => {
                                return networkShareCounts ? ' social-linkz-share-count' : '';
                            })(),
                            style: {
                                margin: '0',
                                '--social-linkz-button-color': props.context.buttonColor,
                                textDecoration: 'none',
                                flexBasis: (() => {
                                    if (props.context.buttonLayout && (props.context.buttonMargin || props.context.buttonMargin == 0)) {
                                        var columns = props.context.buttonLayout.replace(/[^0-9]/g, '');
                                        return 'calc(' + (100 / columns).toFixed(6) + '% - ' + ((columns - 1) * props.context.buttonMargin) / columns + 'px)';
                                    }
                                })()
                            }
                        }),
                        el('span', {className: 'social-linkz-button-wrapper social-linkz-button-block' + props.context.styleClasses.buttonClass},
                            el('span', {
                                    className: 'social-linkz-button-icon social-linkz-button-block' + props.context.styleClasses.iconClass,
                                    style: {
                                        color: (() => {
                                            return !props.context.inverseHover ? props.context.iconColor : ''
                                        })(),
                                        width: !props.context.showLabels ? '100%' : ''
                                    }
                                },
                                networkIcons[props.attributes.network],
                                (() => {
                                    if (networkShareCounts) {
                                        return el('span', {className: 'social-linkz-button-share-count'}, '#');
                                    }
                                })()
                            ),
                            el('span', {
                                    className: 'social-linkz-button-label social-linkz-button-block' + props.context.styleClasses.labelClass,
                                },
                                el('span', {
                                        className: 'social-linkz-button-label-wrapper',
                                        style: {
                                            color: (() => {
                                                return !props.context.inverseHover ? props.context.iconColor : ''
                                            })()
                                        }
                                    },
                                    socialLinkz.networks.share[props.attributes.network].name
                                )
                            )
                        )
                    )
                )
            );
        },
        variations
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
);