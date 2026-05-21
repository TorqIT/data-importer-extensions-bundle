import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { AsTableTransformerForm } from './as-table-transformer-form'

@injectable()
export class DynamicTypeTransformerAsTable extends DynamicTypeTransformerAbstract {
    readonly id = 'asTable'
    readonly label = 'As Table'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <AsTableTransformerForm onChange={ onChange } settings={ settings } />
    }
}
