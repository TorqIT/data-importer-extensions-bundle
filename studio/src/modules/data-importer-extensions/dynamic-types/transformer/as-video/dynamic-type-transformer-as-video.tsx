import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { AsVideoTransformerForm } from './as-video-transformer-form'

@injectable()
export class DynamicTypeTransformerAsVideo extends DynamicTypeTransformerAbstract {
    readonly id = 'asVideo'
    readonly label = 'As Video'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <AsVideoTransformerForm onChange={ onChange } settings={ settings } />
    }
}
