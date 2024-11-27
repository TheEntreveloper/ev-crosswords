import React from 'react';
import {ComponentPreview, Previews} from '@react-buddy/ide-toolbox';
import {PaletteTree} from './palette';
import Edit from "../edit";

const ComponentPreviews = () => {
    return (
        <Previews palette={<PaletteTree/>}>
            <ComponentPreview path="/Edit">
                <Edit/>
            </ComponentPreview>
        </Previews>
    );
};

export default ComponentPreviews;