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
import { Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

export default function Edit() {
    const [ logs, setLogs ] = useState( [] );
    const [ loading, setLoading ] = useState( true );
    const [ error, setError ] = useState( '' );

    /* -------------------------------------------------
     * Load all logs once
     * ------------------------------------------------- */
    useEffect( () => {
        let isMounted = true;

        async function loadLogs() {
            setLoading( true );
            setError( '' );

            try {
                //const res = await fetch('/wp-json/site-prober/v1/logs');
                //const data = await res.json();
                const data = await apiFetch( {
                    path: '/site-prober/v1/logs',
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
    }, [] );

    if (loading) return <Spinner />;
    if (error) return <Notice status="error">{error}</Notice>;
    
    /* -------------------------------------------------
     * Render
     * ------------------------------------------------- */
    return (
        <div className="splv-editor">
            <h3>Site Prober Logs</h3>
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th className="manage-column column-date">Time</th>
                        <th className="manage-column column-user">User</th>
                        <th className="manage-column column-ip">IP</th>
                        <th className="manage-column column-action">Action</th>
                        <th className="manage-column column-object">object</th>
                        <th className="manage-column column-description">Description</th>
                    </tr>
                </thead>
                <tbody>
                    {logs.map(log => (
                        <tr key={log.id}>
                            <td className="column-date">{log.created_at}</td>
                            <td className="column-user">{log.user_id}</td>
                            <td className="column-ip">{log.ip}</td>
                            <td className="column-action">{log.action}</td>
                            <td className="column-object">{log.object_type}</td>
                            <td className="column-description">{log.description}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

