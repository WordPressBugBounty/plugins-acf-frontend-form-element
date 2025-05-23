import Base from 'elementor-frontend/handlers/base';
import {
	changeScrollStatus,
	setHorizontalScrollAlignment,
	setHorizontalTitleScrollValues,
} from 'elementor-frontend-utils/flex-horizontal-scroll';

export default class NestedTabs extends Base {
	constructor( ...args ) {
		super( ...args );

		this.resizeListenerNestedTabs = null;
	}

	/**
	 * @param {string|number} tabIndex
	 *
	 * @return {string}
	 */
	getTabTitleFilterSelector( tabIndex ) {
		return `[data-tab-index="${ tabIndex }"]`;
	}

	/**
	 * @param {string|number} tabIndex
	 *
	 * @return {string}
	 */
	getTabContentFilterSelector( tabIndex ) {
		return `*:nth-child(${ tabIndex })`;
	}

	/**
	 * @param {HTMLElement} tabTitleElement
	 *
	 * @return {string}
	 */
	getTabIndex( tabTitleElement ) {
		return tabTitleElement.getAttribute( 'data-tab-index' );
	}

	getDefaultSettings() {
		return {
			selectors: {
				widgetContainer: '.e-n-tabs',
				tabTitle: '.e-n-tab-title',
				tabTitleText: '.e-n-tab-title-text',
				tabContent: '.e-n-tabs-content > .e-con',
				headingContainer: '.e-n-tabs-heading',
				activeTabContentContainers: '.e-con.e-active',
			},
			classes: {
				active: 'e-active',
			},
			ariaAttributes: {
				titleStateAttribute: 'aria-selected',
				activeTitleSelector: '[aria-selected="true"]',
			},
			showTabFn: 'show',
			hideTabFn: 'hide',
			toggleSelf: false,
			hidePrevious: true,
			autoExpand: true,
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$wdigetContainer: this.findElement( selectors.widgetContainer ),
			$tabTitles: this.findElement( selectors.tabTitle ),
			$tabContents: this.findElement( selectors.tabContent ),
			$headingContainer: this.findElement( selectors.headingContainer ),
		};
	}

	getKeyboardNavigationSettings() {
		return this.getSettings();
	}

	activateDefaultTab() {
		const settings = this.getSettings();

		const defaultActiveTab = this.getEditSettings( 'activeItemIndex' ) || 1,
			originalToggleMethods = {
				showTabFn: settings.showTabFn,
				hideTabFn: settings.hideTabFn,
			};

		// Toggle tabs without animation to avoid jumping
		this.setSettings( {
			showTabFn: 'show',
			hideTabFn: 'hide',
		} );

		this.changeActiveTab( defaultActiveTab );

		// Return back original toggle effects
		this.setSettings( originalToggleMethods );

		this.elements.$wdigetContainer.addClass( 'e-activated' );
	}

	deactivateActiveTab( newTabIndex ) {
		const settings = this.getSettings(),
			activeClass = settings.classes.active,
			activeTitleFilter = settings.ariaAttributes.activeTitleSelector,
			activeContentFilter = '.' + activeClass,
			$activeTitle = this.elements.$tabTitles.filter( activeTitleFilter ),
			$activeContent = this.elements.$tabContents.filter( activeContentFilter );

		this.setTabDeactivationAttributes( $activeTitle, newTabIndex );

		$activeContent.removeClass( activeClass );
		$activeContent[ settings.hideTabFn ]( 0, () => this.onHideTabContent( $activeContent ) );

		return $activeContent;
	}

	getTitleActivationAttributes() {
		const titleStateAttribute = this.getSettings( 'ariaAttributes' ).titleStateAttribute;

		return {
			tabindex: '0',
			[ titleStateAttribute ]: 'true',
		};
	}

	setTabDeactivationAttributes( $activeTitle ) {
		const titleStateAttribute = this.getSettings( 'ariaAttributes' ).titleStateAttribute;

		$activeTitle.attr( {
			tabindex: '-1',
			[ titleStateAttribute ]: 'false',
		} );
	}

	onHideTabContent() {}

	activateTab( tabIndex ) {
		const settings = this.getSettings(),
			activeClass = settings.classes.active,
			animationDuration = 'show' === settings.showTabFn ? 0 : 400;

		let $requestedTitle = this.elements.$tabTitles.filter( this.getTabTitleFilterSelector( tabIndex ) ),
			$requestedContent = this.elements.$tabContents.filter( this.getTabContentFilterSelector( tabIndex ) );

		// Check if the tabIndex exists.
		if ( ! $requestedTitle.length ) {
			// Activate the previous tab and ensure that the tab index is not less than 1.
			const previousTabIndex = Math.max( ( tabIndex - 1 ), 1 );

			$requestedTitle = this.elements.$tabTitles.filter( this.getTabTitleFilterSelector( previousTabIndex ) );
			$requestedContent = this.elements.$tabContents.filter( this.getTabContentFilterSelector( previousTabIndex ) );
		}

		$requestedTitle.attr( this.getTitleActivationAttributes() );
		$requestedContent.addClass( activeClass );

		$requestedContent[ settings.showTabFn ](
			animationDuration,
			() => this.onShowTabContent( $requestedContent ),
		);
	}

	onShowTabContent( $requestedContent ) {
		elementorFrontend.elements.$window.trigger( 'elementor-pro/motion-fx/recalc' );
		elementorFrontend.elements.$window.trigger( 'elementor/nested-tabs/activate', $requestedContent );
		elementorFrontend.elements.$window.trigger( 'elementor/bg-video/recalc' );
	}

	isActiveTab( tabIndex ) {
		return 'true' === this.elements.$tabTitles.filter( '[data-tab-index="' + tabIndex + '"]' ).attr( this.getSettings( 'ariaAttributes' ).titleStateAttribute );
	}

	onTabClick( event ) {
		event.preventDefault();
		this.changeActiveTab( event.currentTarget?.getAttribute( 'data-tab-index' ), true );
	}

	getTabEvents() {
		return {
			click: this.onTabClick.bind( this ),
		};
	}

	getHeadingEvents() {
		const navigationWrapper = this.elements.$headingContainer[ 0 ];

		return {
			mousedown: changeScrollStatus.bind( this, navigationWrapper ),
			mouseup: changeScrollStatus.bind( this, navigationWrapper ),
			mouseleave: changeScrollStatus.bind( this, navigationWrapper ),
			mousemove: setHorizontalTitleScrollValues.bind( this, navigationWrapper, this.getHorizontalScrollSetting() ),
		};
	}

	bindEvents() {
		this.elements.$tabTitles.on( this.getTabEvents() );
		this.elements.$headingContainer.on( this.getHeadingEvents() );

		const settingsObject = {
			element: this.elements.$headingContainer[ 0 ],
			direction: this.getTabsDirection(),
			justifyCSSVariable: '--n-tabs-heading-justify-content',
			horizontalScrollStatus: this.getHorizontalScrollSetting(),
		};

		this.resizeListenerNestedTabs = setHorizontalScrollAlignment.bind( this, settingsObject );
		elementorFrontend.elements.$window.on( 'resize', this.resizeListenerNestedTabs );

		elementorFrontend.elements.$window.on( 'resize', this.setTouchMode.bind( this ) );
		elementorFrontend.elements.$window.on( 'elementor/nested-tabs/activate', this.reInitSwipers );
		elementorFrontend.elements.$window.on( 'elementor/nested-elements/activate-by-keyboard', this.changeActiveTabByKeyboard.bind( this ) );
		elementorFrontend.elements.$window.on( 'elementor/nested-container/atomic-repeater', this.linkContainer.bind( this ) );
	}

	unbindEvents() {
		this.elements.$tabTitles.off();
		this.elements.$headingContainer.off();
		this.elements.$tabContents.children().off();
		elementorFrontend.elements.$window.off( 'resize' );
		elementorFrontend.elements.$window.off( 'elementor/nested-tabs/activate' );
	}

	/**
	 * Fixes issues where Swipers that have been initialized while a tab is not visible are not properly rendered
	 * and when switching to the tab the swiper will not respect any of the chosen `autoplay` related settings.
	 *
	 * This is triggered when switching to a nested tab, looks for Swipers in the tab content and reinitializes them.
	 *
	 * @param {Object} event   - Incoming event.
	 * @param {Object} content - Active nested tab dom element.
	 */
	reInitSwipers( event, content ) {
		const swiperElements = content.querySelectorAll( `.${ elementorFrontend.config.swiperClass }` );

		for ( const element of swiperElements ) {
			if ( ! element.swiper ) {
				return;
			}

			element.swiper.initialized = false;
			element.swiper.init();
		}
	}

	onInit( ...args ) {
		super.onInit( ...args );

		if ( this.getSettings( 'autoExpand' ) ) {
			this.activateDefaultTab();
		}

		const settingsObject = {
			element: this.elements.$headingContainer[ 0 ],
			direction: this.getTabsDirection(),
			justifyCSSVariable: '--n-tabs-heading-justify-content',
			horizontalScrollStatus: this.getHorizontalScrollSetting(),
		};

		setHorizontalScrollAlignment( settingsObject );

		this.setTouchMode();

		if ( 'nested-tabs.default' === this.getSettings( 'elementName' ) ) {
			new elementorModules.frontend.handlers.NestedTitleKeyboardHandler( this.getKeyboardNavigationSettings() );
		}
	}

	onEditSettingsChange( propertyName, value ) {
		if ( 'activeItemIndex' === propertyName ) {
			this.changeActiveTab( value, false );
		}
	}

	onElementChange( propertyName ) {
		if ( this.checkSliderPropsToWatch( propertyName ) ) {
			const settingsObject = {
				element: this.elements.$headingContainer[ 0 ],
				direction: this.getTabsDirection(),
				justifyCSSVariable: '--n-tabs-heading-justify-content',
				horizontalScrollStatus: this.getHorizontalScrollSetting(),
			};

			setHorizontalScrollAlignment( settingsObject );
		}
	}

	checkSliderPropsToWatch( propertyName ) {
		return 0 === propertyName.indexOf( 'horizontal_scroll' ) ||
			'breakpoint_selector' === propertyName ||
			0 === propertyName.indexOf( 'tabs_justify_horizontal' ) ||
			0 === propertyName.indexOf( 'tabs_title_space_between' );
	}

	/**
	 * @param {string}  tabIndex
	 * @param {boolean} fromUser - Whether the call is caused by the user or internal.
	 */
	changeActiveTab( tabIndex, fromUser = false ) {
		// `document/repeater/select` is used only in the editor, only when the element
		// is in the currently-edited document, and only when its not internal call,
		if ( fromUser && this.isEdit && this.isElementInTheCurrentDocument() ) {
			return window.top.$e.run( 'document/repeater/select', {
				container: elementor.getContainer( this.$element.attr( 'data-id' ) ),
				index: parseInt( tabIndex ),
			} );
		}

		const isActiveTab = this.isActiveTab( tabIndex ),
			settings = this.getSettings();

		if ( ( settings.toggleSelf || ! isActiveTab ) && settings.hidePrevious ) {
			this.deactivateActiveTab( tabIndex );
		}

		if ( ! settings.hidePrevious && isActiveTab ) {
			this.deactivateActiveTab( tabIndex );
		}

		if ( ! isActiveTab ) {
			if ( this.isAccordionVersion() ) {
				this.activateMobileTab( tabIndex );
				return;
			}

			this.activateTab( tabIndex );
		}
	}

	changeActiveTabByKeyboard( event, settings ) {
		if ( settings.widgetId.toString() !== this.getID().toString() ) {
			return;
		}

		this.changeActiveTab( settings.titleIndex, true );
	}

	activateMobileTab( tabIndex ) {
		// Timeout time added to ensure that opening of the active tab starts after closing the other tab on Apple devices.
		setTimeout( () => {
			this.activateTab( tabIndex );
			this.forceActiveTabToBeInViewport( tabIndex );
		}, 10 );
	}

	forceActiveTabToBeInViewport( tabIndex ) {
		if ( ! elementorFrontend.isEditMode() ) {
			return;
		}

		const $activeTabTitle = this.elements.$tabTitles.filter( this.getTabTitleFilterSelector( tabIndex ) );

		if ( ! elementor.helpers.isInViewport( $activeTabTitle[ 0 ] ) ) {
			$activeTabTitle[ 0 ].scrollIntoView( { block: 'center' } );
		}
	}

	getActiveClass() {
		const settings = this.getSettings();

		return settings.classes.active;
	}

	getTabsDirection() {
		const currentDevice = elementorFrontend.getCurrentDeviceMode();
		return elementorFrontend.utils.controls.getResponsiveControlValue( this.getElementSettings(), 'tabs_justify_horizontal', '', currentDevice );
	}

	getHorizontalScrollSetting() {
		const currentDevice = elementorFrontend.getCurrentDeviceMode();
		return elementorFrontend.utils.controls.getResponsiveControlValue( this.getElementSettings(), 'horizontal_scroll', '', currentDevice );
	}

	isAccordionVersion() {
		return 'contents' === this.elements.$headingContainer.css( 'display' );
	}

	setTouchMode() {
		const widgetSelector = this.getSettings( 'selectors' ).widgetContainer;

		if ( elementorFrontend.isEditMode() || 'resize' === event?.type ) {
			const responsiveDevices = [ 'mobile', 'mobile_extra', 'tablet', 'tablet_extra' ],
				currentDevice = elementorFrontend.getCurrentDeviceMode();

			if ( -1 !== responsiveDevices.indexOf( currentDevice ) ) {
				this.$element.find( widgetSelector ).attr( 'data-touch-mode', 'true' );
				return;
			}
		} else if ( 'ontouchstart' in window ) {
			this.$element.find( widgetSelector ).attr( 'data-touch-mode', 'true' );
			return;
		}

		this.$element.find( widgetSelector ).attr( 'data-touch-mode', 'false' );
	}

	linkContainer( event ) {
		const { container } = event.detail,
			id = container.model.get( 'id' ),
			currentId = this.$element.data( 'id' );

		if ( id === currentId ) {
			this.updateIndexValues();
			this.updateListeners();

			elementor.$preview[ 0 ].contentWindow.dispatchEvent( new CustomEvent( 'elementor/elements/link-data-bindings' ) );
		}
	}

	updateListeners() {
		elementorFrontend.elementsHandler.runReadyTrigger( this.$element[ 0 ] );
	}

	updateIndexValues() {
		const { $tabContents, $tabTitles } = this.getDefaultElements(),
			settings = this.getSettings(),
			itemIdBase = $tabTitles[ 0 ].getAttribute( 'id' ).slice( 0, -1 ),
			containerIdBase = $tabContents[ 0 ].getAttribute( 'id' ).slice( 0, -1 );

		$tabTitles.each( ( index, element ) => {
			const newIndex = index + 1,
				updatedTabID = itemIdBase + newIndex,
				updatedContainerID = containerIdBase + newIndex;

			element.setAttribute( 'id', updatedTabID );
			element.setAttribute( 'style', `--n-tabs-title-order: ${ newIndex }` );
			element.setAttribute( 'data-tab-index', newIndex );
			element.querySelector( settings.selectors.tabTitleText ).setAttribute( 'data-binding-index', newIndex );
			element.querySelector( settings.selectors.tabTitleText ).setAttribute( 'aria-controls', updatedTabID );
			$tabContents[ index ].setAttribute( 'aria-labelledby', updatedTabID );
			$tabContents[ index ].setAttribute( 'data-tab-index', updatedTabID );
			$tabContents[ index ].setAttribute( 'id', updatedContainerID );
			$tabContents[ index ].setAttribute( 'style', `--n-tabs-title-order: ${ newIndex }` );
		} );
	}
}
