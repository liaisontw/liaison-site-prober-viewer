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

export default function Edit( { attributes, setAttributes } ) {
    const { selectedPlugin } = attributes;

    const [plugins, setPlugins] = useState([]);
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    // 1) Fetch plugin names once
    useEffect(() => {
        async function loadPlugins() {
            try {
                const res = await fetch('/wp-json/site-prober/v1/plugins');
                const data = await res.json();
                setPlugins(data || []);
            } catch (err) {
                setError('Failed to load plugins.');
            }
        }
        loadPlugins();
    }, []);

    // 2) Fetch logs when plugin changes
    useEffect(() => {
        if (!selectedPlugin) return;

        async function loadLogs() {
            setLoading(true);
            setError('');

            try {
                const res = await fetch(`/wp-json/site-prober/v1/logs?plugin=${selectedPlugin}`);
                const data = await res.json();
                setLogs(data || []);
            } catch (err) {
                setError('Failed to load logs.');
            }

            setLoading(false);
        }

        loadLogs();
    }, [selectedPlugin]);

    return (
        <div className="splv-editor">
            <SelectControl
                label="Select Plugin"
                value={selectedPlugin}
                options={[
                    { label: 'Select a plugin', value: '' },
                    ...plugins.map(p => ({ label: p, value: p }))
                ]}
                onChange={(value) => setAttributes({ selectedPlugin: value })}
            />

            {loading && <Spinner />}

            {error && <Notice status="error">{error}</Notice>}

            <ul>
                {logs.map((log, idx) => (
                    <li key={idx}>{log.message}</li>
                ))}
            </ul>
        </div>
    );
}
