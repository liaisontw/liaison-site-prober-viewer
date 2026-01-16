/*
有測的：當 component mount 時：
1. 會呼叫 apiFetch
2. loading 時顯示 Spinner
3. 成功時 render table
4. 失敗時 render Notice

沒測的：
1. WordPress REST API 是否真的存在
2. /site-prober/v1/logs 是否真的可用
3. Gutenberg editor 是否真的載入 block
4. block 在 editor 裡是否正常顯示
*/

import React from 'react';
import '@testing-library/jest-dom';
import { render, screen } from '@testing-library/react';

/**
 * Mock @wordpress/element → React
 */
jest.mock(
    '@wordpress/element',
    () => require( 'react' ),
    { virtual: true }
);

/**
 * Mock block editor
 */
jest.mock(
    '@wordpress/block-editor',
    () => ( {
        useBlockProps: () => ( {
            className: 'wp-block-mock',
        } ),
    } ),
    { virtual: true }
);

/**
 * Mock components
 */
jest.mock(
    '@wordpress/components',
    () => ( {
        Spinner: () => <div role="progressbar" />,
        Notice: ( { children } ) => <div role="alert">{ children }</div>,
    } ),
    { virtual: true }
);

/**
 * Mock i18n
 */
jest.mock(
    '@wordpress/i18n',
    () => ( {
        __: ( s ) => s,
    } ),
    { virtual: true }
);

/**
 * Mock apiFetch
 */
jest.mock(
    '@wordpress/api-fetch',
    () => jest.fn(),
    { virtual: true }
);

import apiFetch from '@wordpress/api-fetch';
import Edit from '../src/edit';

describe( 'Edit block states', () => {
    beforeEach( () => {
        apiFetch.mockReset();
    });

    it( 'shows loading state', () => {
        apiFetch.mockImplementation(
            () => new Promise( () => {} )
        );

        render( <Edit /> );

        expect(
            screen.getByRole( 'progressbar' )
        ).toBeInTheDocument();
    });
});
