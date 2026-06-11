import React from 'react'
import { Form } from '@pimcore/studio-ui-bundle/components'
import { FieldWidthProvider } from '@pimcore/studio-ui-bundle/modules/element'

interface TransformerSettingsLayoutProps {
    children: React.ReactNode
}

export const TransformerSettingsLayout = ({ children }: TransformerSettingsLayoutProps): React.JSX.Element => (
    <FieldWidthProvider>
        <Form
            colon={ false }
            layout="vertical"
        >
            { children }
        </Form>
    </FieldWidthProvider>
)
