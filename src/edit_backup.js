/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

import { useState, useEffect } from '@wordpress/element';
import { SelectControl, Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

export default function Edit( { attributes, setAttributes } ) {
    const { selectedPlugin } = attributes;

    const [ plugins, setPlugins ] = useState( [] );
    const [ logs, setLogs ] = useState( [] );
    const [ loading, setLoading ] = useState( false );
    const [ error, setError ] = useState( '' );

    /* -------------------------------------------------
     * Load plugin list (once)
     * ------------------------------------------------- */
    useEffect( () => {
        let isMounted = true;

        async function loadPlugins() {
            try {
                const data = await apiFetch( {
                    path: '/site-prober/v1/plugins',
                } );

                if ( isMounted ) {
                    setPlugins( Array.isArray( data ) ? data : [] );
                }
            } catch ( err ) {
                if ( isMounted ) {
                    setError( 'Failed to load plugins.' );
                }
            }
        }

        loadPlugins();

        return () => {
            isMounted = false;
        };
    }, [] );

    /* -------------------------------------------------
     * Load logs when plugin changes
     * ------------------------------------------------- */
    useEffect( () => {
        if ( ! selectedPlugin ) {
            setLogs( [] );
            return;
        }

        let isMounted = true;

        async function loadLogs() {
            setLoading( true );
            setError( '' );

            try {
                const data = await apiFetch( {
                    path: `/site-prober/v1/logs?plugin=${ encodeURIComponent(
                        selectedPlugin
                    ) }`,
                } );

                if ( isMounted ) {
                    setLogs( data.logs || [] );
                }
            } catch ( err ) {
                if ( isMounted ) {
                    setError( 'Failed to load logs.' );
                }
            } finally {
                if ( isMounted ) {
                    setLoading( false );
                }
            }
        }

        loadLogs();

        return () => {
            isMounted = false;
        };
    }, [ selectedPlugin ] );

    /* -------------------------------------------------
     * Render
     * ------------------------------------------------- */
    return (
        <div className="splv-editor">
            <SelectControl
                label="Select Plugin"
                value={ selectedPlugin }
                options={ [
                    { label: 'Select a plugin', value: '' },
                    ...plugins.map( ( plugin ) => ( {
                        label: plugin,
                        value: plugin,
                    } ) ),
                ] }
                onChange={ ( value ) =>
                    setAttributes( { selectedPlugin: value } )
                }
            />

            { loading && <Spinner /> }

            { error && <Notice status="error">{ error }</Notice> }

            { ! loading && ! error && logs.length === 0 && selectedPlugin && (
                <Notice status="info">No logs found.</Notice>
            ) }

            <ul className="splv-log-list">
                { logs.map( ( log ) => (
                    <li key={ log.id }>
                        <strong>{ log.plugin }</strong>
                        <br />
                        { log.message }
                        <br />
                        <small>{ log.created_at }</small>
                    </li>
                ) ) }
            </ul>
        </div>
    );
}

